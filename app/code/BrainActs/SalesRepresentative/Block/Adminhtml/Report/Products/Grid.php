<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Block\Adminhtml\Report\Products;

use BrainActs\SalesRepresentative\Block\Adminhtml\Report\AbstractGrid;

/**
 * Class Grid
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class Grid extends AbstractGrid
{
    /**
     * GROUP BY criteria
     *
     * @var string
     */
    public $columnGroupBy = 'period';

    protected function _construct()//@codingStandardsIgnoreLine
    {
        parent::_construct();
        $this->setCountTotals(true);
    }

    /**
     * Return Class name
     * @return string
     */
    public function getResourceCollectionName()
    {
        return \BrainActs\SalesRepresentative\Model\ResourceModel\Report\Products\Collection::class;
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()//@codingStandardsIgnoreLine
    {
        $this->addColumn(
            'period',
            [
                'header' => __('Interval'),
                'index' => 'period',
                'sortable' => false,
                'header_css_class' => 'col-period',
                'column_css_class' => 'col-period',
                'period_type' => $this->getPeriodType(),
                'renderer' => \Magento\Reports\Block\Adminhtml\Sales\Grid\Column\Renderer\Date::class,
                'totals_label' => __('Total'),
                'html_decorators' => ['nobr']

            ]
        );

        $this->addColumn(
            'name',
            [
                'header' => __('Member'),
                'index' => 'name',
                'sortable' => false
            ]
        );

        $this->addColumn(
            'product_name',
            [
                'header' => __('Product'),
                'index' => 'product_name',
                'header_css_class' => 'col-product',
                'column_css_class' => 'col-product',
                'type' => 'string',
                'total'=>'sum',
                'sortable' => false

            ]
        );

        if ($this->getFilterData()->getStoreIds()) {
            $this->setStoreIds(explode(',', $this->getFilterData()->getStoreIds()));
        }
        $currencyCode = $this->getCurrentCurrencyCode();

        $this->addColumn(
            'product_price',
            [
                'header' => __('Price'),
                'type' => 'currency',
                'index' => 'product_price',
                'sortable' => false,
                'header_css_class' => 'col-price',
                'column_css_class' => 'col-price',
                'currency_code' => $currencyCode,
                'rate' => $this->getRate($currencyCode),
            ]
        );

        $this->addColumn(
            'qty_ordered',
            [
                'header' => __('Order Quantity'),
                'index' => 'qty_ordered',
                'type' => 'number',
                'header_css_class' => 'col-qty',
                'column_css_class' => 'col-qty',
                'total' => 'sum',
                'sortable' => false
            ]
        );

        $this->addColumn(
            'increment_order_id',
            [
                'header' => __('Order Id'),
                'index' => 'increment_order_id',
                'type' => 'number',
                'renderer' => \BrainActs\SalesRepresentative\Block\Adminhtml\Report\Column\Renderer\Order::class,
                'sortable' => false,
                'header_css_class' => 'col-qty',
                'column_css_class' => 'col-qty'
            ]
        );

        $this->addColumn(
            'rate_type',
            [
                'header' => __('Rate Type'),
                'index' => 'rate_type',
                'type' => 'number',
                'sortable' => false,
                'header_css_class' => 'col-qty',
                'column_css_class' => 'col-qty',
                'renderer' => \BrainActs\SalesRepresentative\Block\Adminhtml\Report\Column\Renderer\RateType::class
            ]
        );

        $this->addColumn(
            'rate_value',
            [
                'header' => __('Rate Value'),
                'index' => 'rate_value',
                'type' => 'number',
                'header_css_class' => 'col-qty',
                'sortable' => false,
                'column_css_class' => 'col-qty',
                'renderer' => \BrainActs\SalesRepresentative\Block\Adminhtml\Report\Column\Renderer\RateValue::class
            ]
        );

        $this->addColumn(
            'earn',
            [
                'header' => __('Earn'),
                'index' => 'earn',
                'type' => 'currency',
                'total' => 'sum',
                'sortable' => false,
                'header_css_class' => 'col-price',
                'currency_code' => $currencyCode,
                'rate' => $this->getRate($currencyCode),
                'column_css_class' => 'col-price'
            ]
        );

        return parent::_prepareColumns();
    }
}
