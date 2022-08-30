<?php

namespace Designnbuy\Workflow\Plugin\Sales\Order;

class FilterOrderByWorkFlowAdmin {
    /**
     * @var \Designnbuy\Workflow\Helper\Data
     */
    protected $workflowData;
    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_orderFactory;

    public function __construct(
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Designnbuy\Workflow\Helper\Data $workflowData
    ){
        $this->_orderFactory = $orderFactory;
        $this->workflowData = $workflowData;
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

        if($this->workflowData->getWorkflowUser()){
            $viewStatuses = $this->workflowData->getWorkFlowUserViewStatuses();
            $viewStatuses = explode(',',$viewStatuses);

            $orderIds = $orderCollection->getAllIds();
            $order = $this->_orderFactory->create();
            $newOrderIds = [];
            foreach ($orderIds as $orderId) {
                $order = $order->load($orderId);

                foreach ($order->getAllItems() as $orderItem) {
                    if (in_array($orderItem->getWorkflowStatus(), $viewStatuses)){
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
                if (in_array($item->getWorkflowStatus(), $viewStatuses)){
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