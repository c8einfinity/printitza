<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Model\ResourceModel\Report\Products;

/**
 * Class Collection
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class Collection extends \BrainActs\SalesRepresentative\Model\ResourceModel\Report\Collection\AbstractCollection
{
    /**
     * Rating limit
     *
     * @var int
     */
    public $_ratingLimit = 5;//@codingStandardsIgnoreLine

    /**
     * Selected columns
     *
     * @var array
     */
    public $_selectedColumns = [];//@codingStandardsIgnoreLine

    /**
     * Collection constructor.
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Sales\Model\ResourceModel\Report $resource
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $connection
     */
    public function __construct(//@codingStandardsIgnoreLine
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Sales\Model\ResourceModel\Report $resource,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null
    ) {
        $resource->init('brainacts_salesrep_report_product');
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $resource, $connection);
    }

    /**
     * Return ordered filed
     *
     * @return string
     */
    public function getOrderedField()
    {
        return 'qty_ordered';
    }

    /**
     * Retrieve selected columns
     *
     * @return array
     */
    protected function _getSelectedColumns()//@codingStandardsIgnoreLine
    {
        $connection = $this->getConnection();

        if (!$this->_selectedColumns) {
            if ($this->isTotals()) {
                $this->_selectedColumns = $this->getAggregatedColumns();
            } else {
                $this->_selectedColumns = [
                    'period' => sprintf('MAX(%s)', $connection->getDateFormatSql('period', '%Y-%m-%d')),
                    $this->getOrderedField() => 'SUM(' . $this->getOrderedField() . ')',
                    'product_id' => 'product_id',
                    'product_name' => 'MAX(product_name)',
                    'product_price' => 'MAX(product_price)',
                    'earn' => 'earn',
                    'order_id' => 'order_id',
                    'rate_type' => 'rate_type',
                    'rate_value' => 'rate_value',
                    'increment_order_id' => 'increment_order_id'

                ];
            }
        }
        return $this->_selectedColumns;
    }

    /**
     * Init collection select
     *
     * @return $this
     */
    protected function _applyAggregatedTable()//@codingStandardsIgnoreLine
    {
        $select = $this->getSelect();

        $mainTable = $this->getTable('brainacts_salesrep_report_product');
        $memberTable = $this->getTable('brainacts_salesrep_member');
        $select->from($mainTable, $this->_getSelectedColumns());

        if (!$this->isTotals()) {
            $select->join(
                ['member' => $memberTable],
                $mainTable . '.member_id = member.member_id',
                [new \Zend_Db_Expr("CONCAT(firstname,' ',lastname) as name")]//@codingStandardsIgnoreLine
            );
            $select->group(['id', 'period']);//@codingStandardsIgnoreLine
        }

        return $this;
    }

    /**
     * Get SQL for get record count
     *
     * @return \Magento\Framework\DB\Select
     */
    public function getSelectCountSql()
    {
        $this->_renderFilters();
        $select = clone $this->getSelect();
        $select->reset(\Magento\Framework\DB\Select::ORDER);
        return $this->getConnection()->select()->from($select, 'COUNT(*)');
    }

    /**
     * Set ids for store restrictions
     *
     * @param  int|int[] $storeIds
     * @return $this
     */
    public function addStoreRestrictions($storeIds)
    {
        if (!is_array($storeIds)) {
            $storeIds = [$storeIds];
        }
        $currentStoreIds = $this->_storesIds;
        if (isset($currentStoreIds)
            && $currentStoreIds != \Magento\Store\Model\Store::DEFAULT_STORE_ID
            && $currentStoreIds != [\Magento\Store\Model\Store::DEFAULT_STORE_ID]
        ) {
            if (!is_array($currentStoreIds)) {
                $currentStoreIds = [$currentStoreIds];
            }
            $this->_storesIds = array_intersect($currentStoreIds, $storeIds);
        } else {
            $this->_storesIds = $storeIds;
        }

        return $this;
    }

    /**
     * Redeclare parent method for applying filters after parent method
     * but before adding unions and calculating totals
     *
     * @return $this|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     * @throws \Zend_Db_Select_Exception
     */
    protected function _beforeLoad()//@codingStandardsIgnoreLine
    {
        parent::_beforeLoad();

        $this->_applyStoresFilter();

        if ($this->_period) {
            $selects = [];

            $this->_applyDateRangeFilter();

            // add unions to select
            if ($selects) {
                $unionSections = [];
                $cloneSelect = clone $this->getSelect();
                $unionSections[] = '(' . $cloneSelect . ')';
                foreach ($selects as $union) {
                    $unionSections[] = '(' . $union . ')';
                }
                $this->getSelect()->reset()->union($unionSections, \Magento\Framework\DB\Select::SQL_UNION_ALL);//@codingStandardsIgnoreLine
            }

            if ($this->isTotals()) {
                // calculate total
                $cloneSelect = clone $this->getSelect();
                $this->getSelect()->reset()->from($cloneSelect, $this->getAggregatedColumns());
            } else {
                // add sorting
                $this->getSelect()->order(['period ASC', $this->getOrderedField() . ' DESC']);
            }
        }

        return $this;
    }
}
