<?php

namespace Designnbuy\Vendor\Observer;

use Magento\Framework\Event\ObserverInterface;

use Magento\Catalog\Model\Product;
use Magento\Checkout\Model\Cart;


class SetVendorToQuoteItem implements ObserverInterface
{
    protected $_objectManager;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Catalog\Model\Product $product,
        \Magento\Framework\ObjectManagerInterface $interface,
        \Magento\Quote\Model\Quote\Item $quote
    ) {
        $this->_objectManager = $objectManager;
        $this->cart = $cart;
        $this->product = $product;
        $this->objectManager = $interface;
        $this->quote = $quote;
    }
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Quote\Model\Quote\Item $quoteItem */


        $quoteItem = $observer->getEvent()->getQuoteItem();
        $product = $quoteItem->getProduct();
        //$quoteItem = $observer->getQuoteItem();

        $quoteItem->setVendorId($product->getData('vendor_id'));
        return $this;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     * @return bool
     */
    protected function validateItem(\Magento\Quote\Model\Quote\Item $quoteItem)
    {
        if ($quoteItem->getParentItem() || $quoteItem->getChildren()) {
            return false;
        }

        return true;
    }
}