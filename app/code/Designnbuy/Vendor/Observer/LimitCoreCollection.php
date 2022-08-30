<?php

namespace Designnbuy\Vendor\Observer;

use Magento\Framework\Event\ObserverInterface;

use Magento\Catalog\Model\Product;
use Magento\Checkout\Model\Cart;


class LimitCoreCollection implements ObserverInterface
{
    protected $_objectManager;

    /**
     * @var \Designnbuy\Vendor\Helper\Data
     */
    protected $vendorData;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Sales\Model\ResourceModel\Order\Grid\Collection $orderCollection,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Designnbuy\Vendor\Helper\Data $vendorData

    ) {
        $this->_objectManager = $objectManager;
        $this->_orderFactory = $orderFactory;
        $this->_orderCollection = $orderCollection;
        $this->vendorData = $vendorData;
    }
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if($this->vendorData->getVendorUser()){
            $vendorUser = $this->vendorData->getVendorUser();
            $collection = $observer->getEvent()->getCollection();

            if($collection instanceof  \Magento\Sales\Model\ResourceModel\Order\Collection){
                $orderIds = $collection->getAllIds();
                $order = $this->_orderFactory->create();
                $newOrderIds = [];
                foreach ($orderIds as $orderId) {
                    $order = $order->load($orderId);

                    foreach ($order->getAllItems() as $orderItem) {
                        if ($orderItem->getVendorId() == $vendorUser->getUserId()){
                            $newOrderIds[] = $order->getId();
                            break;
                        }
                    }
                }

                $collection->addFieldToFilter('entity_id', array('in' => $newOrderIds));

            }
            if($collection instanceof  \Designnbuy\OrderTicket\Model\ResourceModel\OrderTicket\Grid\Collection){
                $orderIds = $this->_orderCollection->getAllIds();
                $order = $this->_orderFactory->create();
                $newOrderIds = [];
                foreach ($orderIds as $orderId) {
                    $order = $order->load($orderId);

                    foreach ($order->getAllItems() as $orderItem) {
                        if ($orderItem->getVendorId() == $vendorUser->getUserId()){
                            $newOrderIds[] = $order->getId();
                            break;
                        }
                    }
                }
                $collection->addFieldToFilter('order_id', array('in' => $newOrderIds));
            }

        }
    }

}