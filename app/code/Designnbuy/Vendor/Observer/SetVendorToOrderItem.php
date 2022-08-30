<?php

namespace Designnbuy\Vendor\Observer;

use Magento\Framework\Event\ObserverInterface;

use Magento\Catalog\Model\Product;
use Magento\Checkout\Model\Cart;


class SetVendorToOrderItem implements ObserverInterface
{
    protected $_objectManager;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        array $data = []
    ) {
        $this->productRepository = $productRepository;
    }
    public function execute(\Magento\Framework\Event\Observer $observer){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $order = $observer->getEvent()->getData('order');
        
        if (!$order || !$order->getId()) {
            return $this;
        }
        $vendorCommissionPercentage = 0;
        $vendorCommissionAmount = 0;
        foreach ($order->getAllItems() as $orderItem) {
            $product = $orderItem->getProduct();
            $vendorId = $product->getVendorId();

            if($product->getVendorAssignment() == 'auto' && $vendorId){
                $vendor = $objectManager->create('Designnbuy\Vendor\Model\User')
                        ->load($vendorId);
                if($vendor){
                    $vendorCommissionPercentage = $vendor->getCommissionPercentage();
                    $productCommission = $product->getVendorCommission();
                    if($productCommission != ''){
                        $vendorCommissionPercentage = $product->getVendorCommission();
                    }
                    $vendorCommissionAmount += (($orderItem->getPriceInclTax() * $orderItem->getQtyOrdered()) * $vendorCommissionPercentage) / 100;
                }

                $item = $objectManager->create('Magento\Sales\Model\Order\Item')->load($orderItem->getId());
                $item->setVendorId($vendorId);
                $item->setVendorCommission($vendorCommissionAmount);
                $item->save();

            }
        }
    }
}