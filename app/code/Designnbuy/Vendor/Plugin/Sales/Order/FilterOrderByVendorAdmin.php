<?php

namespace Designnbuy\Vendor\Plugin\Sales\Order;

class FilterOrderByVendorAdmin {
    /**
     * @var \Designnbuy\Vendor\Helper\Data
     */
    protected $vendorData;
    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_orderFactory;

    public function __construct(
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Designnbuy\Vendor\Helper\Data $vendorData
    ){
        $this->_orderFactory = $orderFactory;
        $this->vendorData = $vendorData;
    }
    /**
     * Sets a filter on the category collection
     *
     * @param \Magento\Sales\Model\ResourceModel\Order\Collection $order
     * @param \Magento\Framework\Data\Collection $orderCollection
     */
    public function beforeLoad(
        \Magento\Sales\Model\ResourceModel\Order\Grid\Collection $orderCollection, $printQuery = false, $logQuery = false
    ) {

        if($this->vendorData->getVendorUser()){
            $vendorUser = $this->vendorData->getVendorUser();

            $orderIds = $orderCollection->getAllIds();
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

            $orderCollection->addFieldToFilter('entity_id', array('in' => $newOrderIds));
        }
        return [$printQuery, $logQuery];
        //die;
        //$orderIds = [];
        //foreach ($orderCollection as $order) {
          //  echo "<pre>";
          //  print_r($order->getData());
            /*foreach ($order->getAllItems() as $item) {
                if (in_array($item->getVendorStatus(), $viewStatuses)){
                    $orderIds[] = $order->getId();
                    break;
                }

            }*/
       // }
        //echo "<pre>";
        //print_r($orderIds);
        //die;
        //$viewStatuses = explode(',',$viewStatuses);
        //$orderCollection->addFieldToFilter('entity_id', 1);
    }

}