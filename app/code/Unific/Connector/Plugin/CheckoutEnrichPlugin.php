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
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Store\Model\App\Emulation;
use Unific\Connector\Helper\Data\Cart;
use Unific\Connector\Helper\Data\Customer;
use Unific\Connector\Helper\Hmac;
use Unific\Connector\Helper\Message\Queue;
use Unific\Connector\Helper\Settings;

class CheckoutEnrichPlugin extends AbstractPlugin
{
    /**
     * @var Cart
     */
    protected $cartDataHelper;
    /**
     * @var CartRepositoryInterface
     */
    protected $cartRepository;
    /**
     * @var QuoteIdMaskFactory
     */
    protected $quoteIdMaskFactory;
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
     * @param Cart $cartDataHelper
     * @param Customer $customerDataHelper
     * @param CartRepositoryInterface $cartRepository
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param Session $customerSession
     * @param Emulation $emulation
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Hmac $hmacHelper,
        Queue $queueHelper,
        ProductMetadataInterface $productMetadata,
        ModuleListInterface $moduleList,
        Cart $cartDataHelper,
        Customer $customerDataHelper,
        CartRepositoryInterface $cartRepository,
        QuoteIdMaskFactory $quoteIdMaskFactory,
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
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->customerSession = $customerSession;
        $this->customerDataHelper = $customerDataHelper;
    }

    /**
     * @param mixed $cartId
     * @return CartInterface
     * @throws NoSuchEntityException
     */
    protected function getQuote($cartId)
    {
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        $cartId = $quoteIdMask->getQuoteId() ?: $cartId;

        return $this->cartRepository->get($cartId);
    }

    /**
     * @param $cartId
     * @param $address
     */
    protected function callWebhooks($cartId, $address = null)
    {
        try {
            $cart = $this->getQuote($cartId);

            if (!$this->isConnectorEnabled($cart->getStoreId())) {
                return;
            }

            $integrationSubject = 'checkout/update';

            $this->cartDataHelper->setCart($cart);

            if ($address && $address->getFirstname() != null) {
                $this->cartDataHelper->setAddressData($address);
            }

            $this->processWebhook(
                $this->cartDataHelper->getCartInfo(),
                $this->scopeConfig->getValue('unific/webhook/cart_endpoint'),
                Settings::PRIORITY_CART,
                $integrationSubject
            );

            // Send customer
            if ($this->customerSession->isLoggedIn() === false) {
                // Send a customer update, if not logged in, generate a customer from quote data
                if (!ctype_digit((string)$cartId)) {
                    $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
                    $cartId = $quoteIdMask->getQuoteId();
                }

                $this->customerDataHelper->generateGuestCustomer(
                    $this->cartRepository->get($cartId)
                );

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
        } catch (\Exception $e) {
            return;
        }
    }
}
