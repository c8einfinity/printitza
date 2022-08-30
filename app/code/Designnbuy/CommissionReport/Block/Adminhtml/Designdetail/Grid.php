<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Designnbuy\CommissionReport\Block\Adminhtml\Designdetail;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    //protected  $_countTotals = true;

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Designnbuy\CommissionReport\Model\ResourceModel\DesignDetail\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context, $backendHelper, $data);        
    }

    protected function _prepareCollection()
    {
        $id = $this->getRequest()->getParam('id');
        $collection = $this->_collectionFactory->create();
        $collection->addFieldToFilter('item_id', $id);
        /* @var $collection \Designnbuy\CommissionReport\Model\ResourceModel\DesignDetail\Collection */
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'order_id',
            [
                'header' => __('Order Id'),
                'index' => 'order_id',
                'sortable' => false,   
                'html_decorators' => ['nobr'],
                'header_css_class' => 'col-order_id',
                'column_css_class' => 'col-order_id'
            ]
        );

        $this->addColumn(
            'created_at',
            [
                'header' => __('Order Date'),
                'index' => 'created_at',
                'sortable' => false,
                'total' => false,
                'type' => 'datetime',
                'header_css_class' => 'col-status',
                'column_css_class' => 'col-status'
            ]
        );

        $this->addColumn(
            'item_qty',
            [
                'header' => __('Quantity'),
                'type' => 'number',
                'index' => 'item_qty',
                'sortable' => false,
                'sortable' => false,                
                'header_css_class' => 'col-item_qty',
                'column_css_class' => 'col-item_qty'
            ]
        );
        
        $this->addColumn(
            'commission_amount',
            [
                'header' => __('Commission Total'),
                'type' => 'currency',
                'index' => 'commission_amount',
                'sortable' => false,
                'header_css_class' => 'col-commission_amount',
                'column_css_class' => 'col-commission_amount'
            ]
        );

        $this->addColumn(
            'status',
            [
                'header' => __('Order Status'),
                'index' => 'status',
                'type' => 'text',
                'sortable' => false,
                'filter' => false,
                'visibility_filter' => ['show_actual_columns'],
                'header_css_class' => 'col-status',
                'column_css_class' => 'col-status'
            ]
        );
        ///$this->addExportType('*/*/exportOrderCsv', __('CSV'));
        //$this->addExportType('*/*/exportSalesExcel', __('Excel XML'));
        return parent::_prepareColumns();
    }
}
