<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Designnbuy\CommissionReport\Block\Adminhtml\Product;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    const ADMIN_USER = 1;

    /**
     * GROUP BY criteria
     *
     * @var string
     */
    protected $_columnGroupBy = 'order_id';

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Designnbuy\Commission\Model\Commission $commission,
        \Designnbuy\Commission\Helper\Data $commissionHelper,
        \Designnbuy\CommissionReport\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->commissionHelper = $commissionHelper;
        $this->_collectionFactory = $collectionFactory;
        $this->_commission = $commission;
        parent::__construct($context, $backendHelper, $data);        
    }

    
    protected function _construct()
    {
        parent::_construct();
    }

    protected function _prepareCollection()
    {
        $collection = $this->_collectionFactory->create();
        $collection->addFieldToFilter('user_type', 1);

        $ownerUserId = $this->commissionHelper->getOwnerUserId();
        $areaCode = $this->commissionHelper->getAreaCode();
        if($ownerUserId != self::ADMIN_USER && $areaCode == 'adminhtml'):
            $collection->addFieldToFilter('user_id', $ownerUserId);
        endif;
        
        $collection->getSelect()->distinct(true);
        $collection->getSelect()->group('item_purchased_id');

        /* @var $collection \Designnbuy\CommissionReport\Model\ResourceModel\Order\Collection */
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
            'item_qty',
            [
                'header' => __('Total Product Sales'),
                'type' => 'number',
                'index' => 'item_qty',
                'sortable' => true,
                'filter' => false,
                'renderer' => 'Designnbuy\CommissionReport\Block\Adminhtml\Product\Grid\Column\ProductQtySold',
                'header_css_class' => 'col-item_qty',
                'column_css_class' => 'col-item_qty'
            ]
        );
        
        $this->addColumn(
            'commission_amount',
            [
                'header' => __('Total Sales'),
                'type' => 'currency',
                'index' => 'commission_amount',
                'sortable' => false,
                'filter' => false,
                'renderer' => 'Designnbuy\CommissionReport\Block\Adminhtml\Product\Grid\Column\ProductCommissionEarned',
                'header_css_class' => 'col-commission_amount',
                'column_css_class' => 'col-commission_amount'
            ]
        );

        $this->addColumn(
            'action',
            [
                'header' => __('Action'),
                'type' => 'action',
                'index' => 'stores',
                'filter' => false,
                'sortable' => false,
                'renderer' => 'Designnbuy\CommissionReport\Block\Adminhtml\Product\Grid\Column\ProductDetailLink',
                'header_css_class' => 'col-action',
                'visibility_filter' => ['show_actual_columns'],
                'column_css_class' => 'col-action',
                'total' => ""
            ]
        );
        
        ///$this->addExportType('*/*/exportOrderCsv', __('CSV'));
        //$this->addExportType('*/*/exportSalesExcel', __('Excel XML'));
        return parent::_prepareColumns();
    }
}
