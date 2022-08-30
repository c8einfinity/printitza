<?php

namespace Designnbuy\Vendor\Plugin\Sales\OrderTicket;

class FilterOrderTicketByVendorAdmin {
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
        \Designnbuy\OrderTicket\Model\ResourceModel\OrderTicket\Grid\Collection $orderTicketCollection, $printQuery = false, $logQuery = false
    ) {

        if($this->vendorData->getVendorUser()){
            $vendorUser = $this->vendorData->getVendorUser();
            //$order = $this->_orderFactory->create();
            foreach ($orderTicketCollection as $orderTicket) {
                echo "<pre>";
                print_r($orderTicket->getData());
            }
            $orderTickets = $orderTicketCollection->getData();
            /*$newOrderIds = [];
            foreach ($orderTickets as $orderTicket) {
                $orderId = $orderTicket['order_id'];
                $order = $order->load($orderId);
                echo "<pre>";
                print_r($order->getAllItems());
                die;
                foreach ($order->getAllItems() as $orderItem) {
                    if ($orderItem->getVendorId() == $vendorUser->getUserId()){
                        $newOrderIds[] = $order->getId();
                        break;
                    }
                }
            }*/




            $orderTicketCollection->addFieldToFilter('entity_id', array('in' => [137]));
        }
        return [$printQuery, $logQuery];
    }

}