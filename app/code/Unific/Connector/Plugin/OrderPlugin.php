<?php

namespace Unific\Connector\Plugin;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order as ModelOrder;
use Magento\Store\Model\App\Emulation;
use Unific\Connector\Helper\Data\Cart;
use Unific\Connector\Helper\Data\Customer;
use Unific\Connector\Helper\Data\Order;
use Unific\Connector\Helper\Hmac;
use Unific\Connector\Helper\Message\Queue;
use Unific\Connector\Helper\Settings;

class OrderPlugin extends AbstractPlugin
{
    /**
     * @var Order
     */
    protected $orderDataHelper;
    /**
     * @var Cart
     */
    protected $cartDataHelper;
    /**
     * @var CartRepositoryInterface
     */
    protected $cartRepository;
    /**
     * @var Session
     */
    protected $customerSession;
    /**
     * @var Customer
     */
    protected $customerDataHelper;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param Hmac $hmacHelper
     * @param Queue $queueHelper
     * @param ProductMetadataInterface $productMetadata
     * @param ModuleListInterface $moduleList
     * @param Order $orderDataHelper
     * @param Cart $cartDataHelper
     * @param Customer $customerDataHelper
     * @param CartRepositoryInterface $cartRepository
     * @param Session $customerSession
     * @param Emulation $emulation
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Hmac $hmacHelper,
        Queue $queueHelper,
        ProductMetadataInterface $productMetadata,
        ModuleListInterface $moduleList,
        Order $orderDataHelper,
        Cart $cartDataHelper,
        Customer $customerDataHelper,
        CartRepositoryInterface $cartRepository,
        Session $customerSession,
        Emulation $emulation
    ) {
        parent::__construct(
            $scopeConfig,
            $hmacHelper,
            $queueHelper,
            $productMetadata,
            $moduleList,
            $emulation
        );

        $this->cartDataHelper = $cartDataHelper;
        $this->cartRepository = $cartRepository;
        $this->orderDataHelper = $orderDataHelper;
        $this->customerSession = $customerSession;
        $this->customerDataHelper = $customerDataHelper;
    }

    /**
     * @param OrderRepositoryInterface $subject
     * @param callable $proceed
     * @param OrderInterface $entity
     * @return OrderInterface
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function aroundSave(OrderRepositoryInterface $subject, callable $proceed, OrderInterface $entity)
    {
        if (!$this->isConnectorEnabled($entity->getStoreId())) {
            return $proceed($entity);
        }

        // this is the only way to be sure we can detect a new order
        $isNewOrder = $entity->getId() === null;
        $entity = $proceed($entity);

        switch ($entity->getState()) {
            case ModelOrder::STATE_PROCESSING:
                $integrationSubject = $isNewOrder ? 'order/create' : 'order/update';
                break;
            case ModelOrder::STATE_HOLDED:
                $integrationSubject = 'order/update';
                break;

            case ModelOrder::STATE_CANCELED:
                $integrationSubject = 'order/cancel';
                break;

            case ModelOrder::STATE_COMPLETE:
                $integrationSubject = 'order/ship';
                break;

            case ModelOrder::STATE_CLOSED:
                $integrationSubject = 'order/refund';
                break;
            default:
                $integrationSubject = 'order/create';
                break;
        }

        // Send the order
        $this->orderDataHelper->setOrder($entity);
        $this->emulateStore($entity->getStoreId());
        $this->processWebhook(
            $this->orderDataHelper->getOrderInfo(),
            $this->scopeConfig->getValue('unific/webhook/order_endpoint'),
            Settings::PRIORITY_ORDER,
            $integrationSubject
        );

        $cart = $this->getCartForOrder($entity);
        if (!$cart) {
            // if there is no cart we are in admin action after quoe has been removed
            $this->stopEmulation();

            return $entity;
        }

        // Send the cart
        if ($integrationSubject == 'order/create') {
            if ($entity->getQuoteId() != null) {
                $this->cartDataHelper->setCart($cart);
                $this->processWebhook(
                    $this->cartDataHelper->getCartInfo(),
                    $this->scopeConfig->getValue('unific/webhook/cart_endpoint'),
                    Settings::PRIORITY_CART,
                    'checkout/update'
                );
            }
        }

        // Send customer
        if ($integrationSubject == 'order/create') {
            // Send a customer update, if not logged in, generate a customer from order data
            if ($this->customerSession->isLoggedIn()) {
                $this->customerDataHelper->setCustomer($this->customerSession->getCustomer()->getDataModel());
            } else {
                $this->customerDataHelper->generateGuestCustomer($entity);
            }

            $customerData = $this->customerDataHelper->getCustomerInfo();

            if ($customerData['email'] != null && filter_var($customerData['email'], FILTER_VALIDATE_EMAIL)) {
                $this->processWebhook(
                    $customerData,
                    $this->scopeConfig->getValue('unific/webhook/customer_endpoint'),
                    Settings::PRIORITY_CUSTOMER,
                    'customer/update'
                );
            }
        }

        $this->stopEmulation();

        return $entity;
    }

    /**
     * @param OrderInterface $entity
     * @return CartInterface|false
     */
    private function getCartForOrder(OrderInterface $entity)
    {
        try {
            return $this->cartRepository->get($entity->getQuoteId());
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }
}
