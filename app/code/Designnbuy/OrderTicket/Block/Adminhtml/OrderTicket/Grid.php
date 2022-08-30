<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Designnbuy\OrderTicket\Block\Adminhtml\OrderTicket;

/**
 * ORDERTICKET Grid
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * OrderTicket grid collection
     *
     * @var \Designnbuy\OrderTicket\Model\ResourceModel\OrderTicket\Grid\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * OrderTicket model
     *
     * @var \Designnbuy\OrderTicket\Model\OrderTicketFactory
     */
    protected $_orderticketFactory;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $_moduleManager;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Designnbuy\OrderTicket\Model\ResourceModel\OrderTicket\Grid\CollectionFactory $collectionFactory
     * @param \Designnbuy\OrderTicket\Model\OrderTicketFactory $orderticketFactory
     * @param array $data
     */
    public function __construct(
                
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Designnbuy\OrderTicket\Model\ResourceModel\OrderTicket\Grid\CollectionFactory $collectionFactory,
        \Designnbuy\OrderTicket\Model\OrderTicketFactory $orderticketFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_orderticketFactory = $orderticketFactory;        
        parent::__construct($context, $backendHelper, $data);
        $this->_moduleManager = $moduleManager;
    }

    /**
     * Initialize grid
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('orderticketGrid');
        $this->setDefaultSort('date_requested');
        $this->setDefaultDir('DESC');        
    }

    /**
     * Prepare related item collection
     *
     * @return \Designnbuy\OrderTicket\Block\Adminhtml\OrderTicket\Grid
     */
    protected function _prepareCollection()
    {
        $this->_beforePrepareCollection();
        return parent::_prepareCollection();
    }

    /**
     * Configuring and setting collection
     *
     * @return $this
     */
    protected function _beforePrepareCollection()
    {
        if (!$this->getCollection()) {
            /** @var $collection \Designnbuy\OrderTicket\Model\ResourceModel\OrderTicket\Grid\Collection */
            $collection = $this->_collectionFactory->create();
            $this->setCollection($collection);
        }
        return $this;
    }

    /**
     * Prepare grid columns
     *
     * @return \Designnbuy\OrderTicket\Block\Adminhtml\OrderTicket\Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'increment_id',
            [
                'header' => __('Order Ticket #'),
                'index' => 'increment_id',
                'type' => 'text',
                'header_css_class' => 'col-orderticket-number',
                'column_css_class' => 'col-orderticket-number'
            ]
        );

        $this->addColumn(
            'date_requested',
            [
                'header' => __('Requested'),
                'index' => 'date_requested',
                'type' => 'datetime',
                'html_decorators' => ['nobr'],
                'header_css_class' => 'col-period',
                'column_css_class' => 'col-period'
            ]
        );

        $this->addColumn(
            'order_increment_id',
            [
                'header' => __('Order'),
                'type' => 'text',
                'index' => 'order_increment_id',
                'header_css_class' => 'col-order-number',
                'column_css_class' => 'col-order-number'
            ]
        );

        if ($this->_moduleManager->isEnabled('Designnbuy_JobManagement')) 
        {
            $this->addColumn(
                'job_id',
                [
                    'header' => __('Job Id'),
                    'type' => 'text',
                    'index' => 'job_id',
                    'header_css_class' => 'col-order-number',
                    'column_css_class' => 'col-order-number'
                ]
            );    
        }

        $this->addColumn(
            'order_date',
            [
                'header' => __('Ordered'),
                'index' => 'order_date',
                'type' => 'datetime',
                'html_decorators' => ['nobr'],
                'header_css_class' => 'col-period',
                'column_css_class' => 'col-period'
            ]
        );

        $this->addColumn(
            'customer_name',
            [
                'header' => __('Customer'),
                'index' => 'customer_name',
                'header_css_class' => 'col-name',
                'column_css_class' => 'col-name'
            ]
        );
        /** @var $orderticketModel \Designnbuy\OrderTicket\Model\OrderTicket */
        $orderticketModel = $this->_orderticketFactory->create();
        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'index' => 'status',
                'type' => 'options',
                'options' => $orderticketModel->getAllStatuses(),
                'header_css_class' => 'col-status',
                'column_css_class' => 'col-status'
            ]
        );

        $this->addColumn(
            'action',
            [
                'header' => __('Action'),
                'type' => 'action',
                'getter' => 'getId',
                'actions' => [
                    [
                        'caption' => __('View'),
                        'url' => ['base' => $this->_getControllerUrl('edit')],
                        'field' => 'id',
                    ],
                ],
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'is_system' => true,
                'header_css_class' => 'col-actions',
                'column_css_class' => 'col-actions'
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * Prepare massaction
     *
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('entity_ids');

        $this->getMassactionBlock()->addItem(
            'status',
            [
                'label' => __('Close'),
                'url' => $this->getUrl($this->_getControllerUrl('close')),
                'confirm' => __(
                    'You have chosen to change status(es) of the selected Order Ticket requests to Close.'
                    . ' Are you sure you want to continue?'
                )
            ]
        );

        return $this;
    }

    /**
     * Get Url to action
     *
     * @param  string $action action Url part
     * @return string
     */
    protected function _getControllerUrl($action = '')
    {
        return '*/*/' . $action;
    }

    /**
     * Retrieve row url
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl($this->_getControllerUrl('edit'), ['id' => $row->getId()]);
    }
}
