<?php

namespace Unific\Connector\Controller\Cart;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;

class Restore extends Action
{
    private $context;
    private $moduleManager;
    private $checkoutSession;
    private $cartRepository;
    private $quoteIdMaskFactory;

    /**
     * Cart constructor.
     * @param Context $context
     * @param ModuleManager $moduleManager
     * @param Session $checkoutSession
     * @param CartRepositoryInterface $cartRepository
     * @param \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory
     */
    public function __construct(
        Context $context,
        ModuleManager $moduleManager,
        Session $checkoutSession,
        CartRepositoryInterface $cartRepository,
        \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory
    ) {
        parent::__construct($context);
        $this->context = $context;
        $this->moduleManager = $moduleManager;
        $this->checkoutSession = $checkoutSession;
        $this->cartRepository = $cartRepository;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
    }

    public function execute()
    {
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($this->getRequest()->getParam('id'), 'masked_id');

        $quote = $this->cartRepository->get($quoteIdMask->getQuoteId());

        // Note - we reactivate the cart if it's not active.
        // This would happen for example when the cart was bought.
        if (!$quote->getIsActive()) {
            $quote->setIsActive(true);
            $this->cartRepository->save($quote);
        }

        if ($quote !== null) {
            $this->checkoutSession->setQuoteId($quote->getId());
        } else {
            throw new LocalizedException('Could not resolve quote for the given restore cart hash');
        }

	return $this->_redirect('checkout/cart');
    }
}
