<?php

namespace Designnbuy\Workflow\Block\Adminhtml;

use Designnbuy\Workflow\Model\ResourceModel\Status\CollectionFactory as StatusCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory as OrderItemCollectionFactory;
use Designnbuy\JobManagement\Model\ResourceModel\Jobmanagement\CollectionFactory as JobCollectionFactory;

/**
 * Template grid container
 * @category Designnbuy
 * @package  Designnbuy_Template
 * @module   Template
 * @author   Designnbuy Developer
 */
class Report extends \Magento\Backend\Block\Template
{
    /**
     * @param StatusCollectionFactory $statusCollectionFactory
     */
    protected $statusCollectionFactory;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory
     */
    protected $_orderItemCollectionFactory;

    /**
     * @var Designnbuy\JobManagement\Model\ResourceModel\Jobmanagement\CollectionFactory
     */
    protected $_jobCollectionFactory;

    /**
     * @var string
     */
    protected $_template = 'Designnbuy_Workflow::report.phtml';
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Designnbuy\Workflow\Model\ResourceModel\Status\CollectionFactory $statusCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        StatusCollectionFactory $statusCollectionFactory,
        OrderItemCollectionFactory $orderItemCollectionFactory,
        JobCollectionFactory $jobCollectionFactory,
        array $data = []
    ) {
        $this->statusCollectionFactory = $statusCollectionFactory;
        $this->_orderItemCollectionFactory = $orderItemCollectionFactory;
        $this->_jobCollectionFactory = $jobCollectionFactory;
        parent::__construct($context, $data);
    }

    public function getWorkFlowItemStatuses()
    {
        $collection = $this->statusCollectionFactory->create();
        $collection->addFieldToFilter('display_on_dashboard', 1);
        $collection->getSelect()->order('position', 'ASC');

        return $collection;
    }

    public function getJobItemsByWorkflowStatus($statusId)
    {
        $collection = $this->_jobCollectionFactory->create();
        $collection->addFieldToFilter('main_table.workflow_status_id', $statusId);
        //$collection->getSelect()->join(array('job_management'=> 'designnbuy_jobmanagement_jobmanagement'), 'job_management.workflow_status_id = main_table.status_id', array('job_management.entity_id'));
        $collection->getSelect()->join(array('sales_order'=> 'sales_order'), 'sales_order.entity_id = main_table.order_id', array('sales_order.increment_id'));

        $collection->getSelect()->group('order_id');
        return $collection;
    }

    /*public function getOrderItemsByWorkflowStatus($statusId)
    {
        $collection = $this->_orderItemCollectionFactory->create();
        $collection->addFieldToFilter('main_table.workflow_status', $statusId);
        $collection->getSelect()->join( array('sales_order'=> 'sales_order'), 'sales_order.entity_id = main_table.order_id', array('sales_order.increment_id'));
        $collection->getSelect()->group('order_id');
        return $collection;
    }*/

    /**
     * Get order link (href address)
     *
     * @return string
     */
    public function getOrderLink($orderId)
    {
        return $this->getUrl('sales/order/view', ['order_id' => $orderId]);
    }

    public function getJobLink($entityId, $orderId)
    {
        return $this->getUrl('jobmanagement/jobmanagement/edit', ['id' => $entityId, 'order_id' => $orderId]);
    }
}
