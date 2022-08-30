<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Designnbuy\CommissionReport\Block\Adminhtml\Designer;

/**
 * Adminhtml sales report grid block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * GROUP BY criteria
     *
     * @var string
     */
    protected $_columnGroupBy = 'order_id';


    //protected  $_countTotals = true;

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Designnbuy\Commission\Model\Commission $commission,
        \Designnbuy\CommissionReport\Model\ResourceModel\Designer\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_commission = $commission;
        parent::__construct($context, $backendHelper, $data);        
    }

    
    protected function _construct()
    {
        parent::_construct();
        //$this->setCountTotals(true);

        /*if (isset($this->_columnGroupBy)) {
            $this->isColumnGrouped($this->_columnGroupBy, true);
        }*/
    }

    public function getCountTotals()
    {
        if (!$this->getTotals()) 
        {
            $filterData = $this->getFilterData();
            $totalsCollection = $this->_collectionFactory->create();

            if ($totalsCollection->load()->getSize() < 1 ) {
                $this->setTotals(new \Magento\Framework\DataObject());
                $this->setCountTotals(false);
            } else 
            {   
                foreach ($totalsCollection->getItems() as $item) 
                {
                    $itemData = $item->getData();
                    //$this->setTotals($item);
                    $this->setTotals(new \Magento\Framework\DataObject());
                    //continue;
                }
            }
        }
        return parent::getCountTotals();
    }

    protected function _prepareCollection()
    {
        $collection = $this->_collectionFactory->create();
        $collection->getSelect()->distinct(true);
        $collection->getSelect()->group('user_id');

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
            'user_name',
            [
                'header' => __('User Name'),
                'index' => 'user_name',
                'sortable' => false,   
                //'totals_label' => __('Design Name'),
                'html_decorators' => ['nobr'],
                'header_css_class' => 'col-order_id',
                'column_css_class' => 'col-order_id'
            ]
        );

        $this->addColumn(
            'user_type',
            [
                'header' => __('User Type'),
                'index' => 'user_type',
                'type' => 'options',
                'options'   =>  array(
                        '2' => 'Designer',
                        '1'  => 'Reseller'
                    ),
                'sortable' => false,
                'renderer' => '\Designnbuy\CommissionReport\Block\Adminhtml\Designer\Grid\Column\DesignerType',
                'visibility_filter' => ['show_actual_columns'],
                'header_css_class' => 'col-revenue',
                'column_css_class' => 'col-revenue'
            ]
        );

        $this->addColumn(
            'item_qty',
            [
                'header' => __('Total Sold Qty'),
                'type' => 'number',
                'index' => 'item_qty',
                'sortable' => false,
                'sortable' => false,
                'renderer' => 'Designnbuy\CommissionReport\Block\Adminhtml\Designer\Grid\Column\DesignQtySold',
                'header_css_class' => 'col-item_qty',
                'column_css_class' => 'col-item_qty'
            ]
        );
        
        $this->addColumn(
            'commission_amount',
            [
                'header' => __('Commission Earned'),
                'type' => 'currency',
                'index' => 'commission_amount',
                'sortable' => false,
                'total' => 'sum',
                'renderer' => 'Designnbuy\CommissionReport\Block\Adminhtml\Designer\Grid\Column\CommissionEarned',
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
                'renderer' => 'Designnbuy\CommissionReport\Block\Adminhtml\Designer\Grid\Column\DetailLink',
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
