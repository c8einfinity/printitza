<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Designnbuy\CommissionReport\Block\Adminhtml\Productdetail;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Designnbuy\CommissionReport\Model\ResourceModel\ProductDetail\CollectionFactory $collectionFactory,
        \Magento\Sales\Ui\Component\Listing\Column\Status\Options $orderOptions,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_orderOptions = $orderOptions;
        parent::__construct($context, $backendHelper, $data);        
    }

    protected function _prepareCollection()
    {
        $id = $this->getRequest()->getParam('id');
        $collection = $this->_collectionFactory->create();
        $collection->addFieldToFilter('item_purchased_id', $id)->addFieldToFilter('user_type', 1);
        $collection->setOrder('order_id','DESC');
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
            'item_name',
            [
                'header' => __('Product Name'),
                'index' => 'item_name',
                'sortable' => false,   
                'html_decorators' => ['nobr'],
                'header_css_class' => 'col-order_id',
                'column_css_class' => 'col-order_id'
            ]
        );

        $this->addColumn(
            'user_name',
            [
                'header' => __('User Name'),
                'index' => 'user_name',
                'sortable' => false,
                'header_css_class' => 'col-status',
                'column_css_class' => 'col-status'
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

        $orderOptions = [];
        foreach ($this->_orderOptions->toOptionArray() as $option) {
            $orderOptions[$option['value']] = $option['label'];
        }

        $this->addColumn(
            'status',
            [
                'header' => __('Order Status'),
                'index' => 'status',
                'type' => 'options',
                /*'sortable' => false,
                'filter' => false,*/
                'visibility_filter' => ['show_actual_columns'],
                'header_css_class' => 'col-status',
                'column_css_class' => 'col-status',
                'options' => $orderOptions
            ]
        );
        ///$this->addExportType('*/*/exportOrderCsv', __('CSV'));
        //$this->addExportType('*/*/exportSalesExcel', __('Excel XML'));
        return parent::_prepareColumns();
    }
}
