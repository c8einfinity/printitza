<?php

namespace Designnbuy\Workflow\Plugin\Sales\Order\Item;

class FilterOrderItemByWorkFlowAdmin {

    /**
     * @var \Designnbuy\Workflow\Helper\Data
     */
    protected $workflowData;

    public function __construct(
        \Designnbuy\Workflow\Helper\Data $workflowData
    ){
        $this->workflowData = $workflowData;
    }
    /**
     * Sets a filter on the category collection
     *
     * @param \Magento\Sales\Model\ResourceModel\Order\Collection $order
     * @param \Magento\Framework\Data\Collection $orderCollection
     */
    public function beforeLoad(
        \Magento\Sales\Model\ResourceModel\Order\Item\Collection $orderCollection, $printQuery = false, $logQuery = false
    ) {
        if($this->workflowData->getWorkflowUser()) {
            $viewStatuses = $this->workflowData->getWorkFlowUserViewStatuses();
            if ($viewStatuses) {
                $viewStatuses = explode(',', $viewStatuses);
                $orderCollection->addFieldToFilter('workflow_status', array('in' => $viewStatuses));
            }
        }
    }

}