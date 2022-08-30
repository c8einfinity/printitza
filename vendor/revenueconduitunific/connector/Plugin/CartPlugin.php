<?php

namespace Unific\Connector\Plugin;

use Magento\Checkout\Model\Cart as ModelCart;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Store\Model\App\Emulation;
use Unific\Connector\Helper\Data\Cart;
use Unific\Connector\Helper\Hmac;
use Unific\Connector\Helper\Message\Queue;
use Unific\Connector\Helper\Settings;

class CartPlugin extends AbstractPlugin
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
     * @var Session
     */
    protected $customerSession;

    /**
     * @param Cart $cartDataHelper
     * @param CartRepositoryInterface $cartRepository
     * @param Session $customerSession
     * @param ScopeConfigInterface $scopeConfig
     * @param Hmac $hmacHelper
     * @param Queue $queueHelper
     * @param ProductMetadataInterface $productMetadata
     * @param ModuleListInterface $moduleList
     * @param Emulation $emulation
     */
    public function __construct(
        Cart $cartDataHelper,
        CartRepositoryInterface $cartRepository,
        Session $customerSession,
        ScopeConfigInterface $scopeConfig,
        Hmac $hmacHelper,
        Queue $queueHelper,
        ProductMetadataInterface $productMetadata,
        ModuleListInterface $moduleList,
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
        $this->customerSession = $customerSession;
    }

    /**
     * @param ModelCart $subject
     * @param ModelCart $result
     * @return ModelCart
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterSave(ModelCart $subject, ModelCart $result)
    {
        if ($this->isConnectorEnabled($subject->getStoreId())) {
            $this->cartDataHelper->setCart($subject->getQuote());

            $integrationSubject = 'checkout/create';

            // Magento adds the created at tag, if the created date differs from the current change
            if ($this->cartDataHelper->getCart()->getCreatedAt() != null) {
                $integrationSubject = 'checkout/update';
            }

            $this->processWebhook(
                $this->cartDataHelper->getCartInfo(),
                $this->scopeConfig->getValue('unific/webhook/cart_endpoint'),
                Settings::PRIORITY_CART,
                $integrationSubject
            );
        }

        return $result;
    }
}
