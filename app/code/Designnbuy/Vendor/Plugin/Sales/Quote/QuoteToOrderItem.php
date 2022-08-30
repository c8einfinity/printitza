<?php
namespace Designnbuy\Vendor\Plugin\Sales\Quote;

use Closure;
use Magento\Quote\Model\Quote\Item\ToOrderItem;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Quote\Model\Quote\Item\AbstractItem;

class QuoteToOrderItem
{

    /**
     * Add vendor attributes to order data
     *
     * @param ToOrderItem $subject
     * @param OrderItemInterface $orderItem
     * @param AbstractItem $item
     * @param array $data
     * @return OrderItemInterface
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterConvert(ToOrderItem $subject, OrderItemInterface $orderItem, AbstractItem $item, $data = [])
    {
        //$orderItem->setVendorId($item->getProduct()->getVendorId());
        return $orderItem;
    }


    /**
     * @param \Magento\Quote\Model\Quote\Item\ToOrderItem $subject
     * @param callable $proceed
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @param array $additional
     * @return \Magento\Sales\Model\Order\Item
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundConvert(
        \Magento\Quote\Model\Quote\Item\ToOrderItem $subject,
        Closure $proceed,
        \Magento\Quote\Model\Quote\Item\AbstractItem $item,
        $additional = []
    ) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var $orderItem \Magento\Sales\Model\Order\Item */
        $orderItem = $proceed($item, $additional);//result of function 'convert' in class 'Magento\Quote\Model\Quote\Item\ToOrderItem'

        //$orderItem->setVendorCommission(9);
        $product = $orderItem->getProduct();

        $vendorId = $product->getVendorId();

        $vendorCommissionPercentage = 0;
        $vendorCommissionAmount = 0;

        if($product->getVendorAssignment() == 'auto' && $vendorId){
            $orderItem->setVendorId($product->getVendorId());//set your required
            $vendor = $objectManager->create('Designnbuy\Vendor\Model\User')
                ->load($vendorId);

            if($vendor){
                $vendorCommissionPercentage = $vendor->getCommissionPercentage();
                $productCommission = $product->getVendorCommission();

                if($productCommission != ''){
                    $vendorCommissionPercentage = $product->getVendorCommission();
                }
                $vendorCommissionAmount = (($orderItem->getPriceInclTax() * $orderItem->getQtyOrdered()) * $vendorCommissionPercentage) / 100;
            }
            
            $orderItem->setVendorCommission($vendorCommissionAmount);
        }


        return $orderItem;// return an object '$orderItem' which will replace result of function 'convert' in class 'Magento\Quote\Model\Quote\Item\ToOrderItem'
    }

}