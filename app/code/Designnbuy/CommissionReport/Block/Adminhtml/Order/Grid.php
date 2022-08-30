<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Designnbuy\CommissionReport\Block\Adminhtml\Order;

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

    protected $_countTotals = true;

    protected $_resellerHelper;

    protected $authSession;
    //protected  $_countTotals = true;

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Designnbuy\Commission\Model\Commission $commission,
        \Designnbuy\CommissionReport\Model\ResourceModel\Order\CollectionFactory $collectionFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Designnbuy\Reseller\Helper\Data $resellerHelper,
        \Magento\Sales\Ui\Component\Listing\Column\Status\Options $orderOptions,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_commission = $commission;
        $this->authSession = $authSession;
        $this->_resellerHelper = $resellerHelper;
        $this->_orderOptions = $orderOptions;
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

    protected function _prepareCollection()
    {
        $collection = $this->_collectionFactory->create();
        $filterData = $this->getFilterData();
        $collection->setOrder('order_id','DESC');
        /* @var $collection \Designnbuy\CommissionReport\Model\ResourceModel\Order\Collection */
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }



    public function getTotals()
    {
        //$totals = new Varien_Object();
        $totals = new \Magento\Framework\DataObject();

        $fields = array(
            'commission_amount' => 0,
        );
        foreach ($this->getCollection() as $item) {

            foreach($fields as $field=>$value){
                $fields[$field] += $item->getData($field);
            }
        }

        //First column in the grid
        $fields['order_id']='Totals';
        $totals->setData($fields);
        return $totals;
    }


    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        $user = $this->authSession->getUser();
        $resellerId = $this->_resellerHelper->isResellerUser($user->getId());
        
        $this->addColumn(
            'order_id',
            [
                'header' => __('Order Id'),
                'index' => 'order_id',
                'sortable' => false,
                'totals_label' => __('Total'),
                'html_decorators' => ['nobr'],
                'header_css_class' => 'col-order_id',
                'column_css_class' => 'col-order_id'
            ]
        );
        if (!$resellerId) {
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
                'renderer' => '\Designnbuy\CommissionReport\Block\Adminhtml\Order\Grid\Column\DesignerType',
                'visibility_filter' => ['show_actual_columns'],
                'header_css_class' => 'col-revenue',
                'column_css_class' => 'col-revenue'
            ]
        );
        }
        $this->addColumn(
            'user_name',
            [
                'header' => __('Designer/Reseller Name'),
                'index' => 'user_name',
                'sortable' => false,
                'total' => false,
                'header_css_class' => 'col-status',
                'column_css_class' => 'col-status'
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
                'header_css_class' => 'col-invoiced',
                'column_css_class' => 'col-invoiced'
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
                //'sortable' => false,
                //'filter' => false,
                'header_css_class' => 'col-status',
                'column_css_class' => 'col-status',
                'options' => $orderOptions
            ]
        );

        $this->addColumn(
            'created_at',
            [
                'header' => __('Created At'),
                'index' => 'created_at',
                'type' => 'datetime',
                'sortable' => false,
                'header_css_class' => 'col-status',
                'column_css_class' => 'col-status'
            ]
        );

        $this->addColumn(
            'updated_at',
            [
                'header' => __('Updated At'),
                'index' => 'updated_at',
                'type' => 'datetime',
                'sortable' => false,
                'header_css_class' => 'col-status',
                'column_css_class' => 'col-status'
            ]
        );

        //$this->addExportType('*/*/exportOrderCsv', __('CSV'));
        //$this->addExportType('*/*/exportSalesExcel', __('Excel XML'));
        return parent::_prepareColumns();
    }
}
