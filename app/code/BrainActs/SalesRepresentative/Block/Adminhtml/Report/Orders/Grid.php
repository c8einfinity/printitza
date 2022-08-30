<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Block\Adminhtml\Report\Orders;

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
     * @return string
     */
    public function getResourceCollectionName()
    {
        return \BrainActs\SalesRepresentative\Model\ResourceModel\Report\Orders\Collection::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareColumns()//@codingStandardsIgnoreLine
    {
        $this->addColumn(
            'period',
            [
                'header' => __('Interval'),
                'index' => 'period',
                'sortable' => false,
                'period_type' => $this->getPeriodType(),
                'renderer' => \Magento\Reports\Block\Adminhtml\Sales\Grid\Column\Renderer\Date::class,
                'totals_label' => __('Total'),
                'html_decorators' => ['nobr'],
                'header_css_class' => 'col-period',
                'column_css_class' => 'col-period'
            ]
        );

        if ($this->getFilterData()->getStoreIds()) {
            $this->setStoreIds(explode(',', $this->getFilterData()->getStoreIds()));
        }
        $currencyCode = $this->getCurrentCurrencyCode();

        $this->addColumn(
            'name',
            [
                'header' => __('Member'),
                'index' => 'name',
                'sortable' => false,
            ]
        );

        $this->addColumn(
            'order_price',
            [
                'header' => __('Price'),
                'type' => 'currency',
                'currency_code' => $currencyCode,
                'index' => 'order_price',
                'sortable' => false,
                'rate' => $this->getRate($currencyCode),
                'header_css_class' => 'col-price',
                'column_css_class' => 'col-price',
                'total'=>'sum'
            ]
        );

        $this->addColumn(
            'increment_order_id',
            [
                'header' => __('Order'),
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
                'renderer' => \BrainActs\SalesRepresentative\Block\Adminhtml\Report\Column\Renderer\RateType::class,
                'sortable' => false,
                'header_css_class' => 'col-qty',
                'column_css_class' => 'col-qty'
            ]
        );

        $this->addColumn(
            'rate_value',
            [
                'header' => __('Rate Value'),
                'index' => 'rate_value',
                'type' => 'number',
                'sortable' => false,
                'renderer' => \BrainActs\SalesRepresentative\Block\Adminhtml\Report\Column\Renderer\RateValue::class,
                'header_css_class' => 'col-qty',
                'column_css_class' => 'col-qty'
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
                'currency_code' => $currencyCode,
                'rate' => $this->getRate($currencyCode),
                'header_css_class' => 'col-price',
                'column_css_class' => 'col-price'
            ]
        );

        return parent::_prepareColumns();
    }
}
