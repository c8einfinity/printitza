<?php
/**
 * Copyright © 2019 Designnbuy WebToPrint Pvt. Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 * ALWAYS DO BETTER @a
 */

namespace Drc\Storepickup\Model\ResourceModel\Holidays;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * ID Field Name
     *
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'drc_storepickup_holidays_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'holidays_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Drc\Storepickup\Model\Holidays', 'Drc\Storepickup\Model\ResourceModel\Holidays');
    }

    protected function _initSelect()
    {
        parent::_initSelect();
    }
    public function addStoreFilter()
    {
        $this->getSelect()->joinLeft(
            ['store' => $this->getTable('drc_storepickup_holidays_store')],
            'main_table.entity_id = store.holidays_id',
            ['store_id']
        );
        return $this;
    }
    /**
     * Get SQL for get record count.
     * Extra GROUP BY strip added.
     *
     * @return \Magento\Framework\DB\Select
     */
    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(\Zend_Db_Select::GROUP);
        return $countSelect;
    }
    /**
     * @param string $valueField
     * @param string $labelField
     * @param array $additional
     * @return array
     */
    protected function _toOptionArray($valueField = 'entity_id', $labelField = 'title', $additional = [])
    {
        return parent::_toOptionArray($valueField, $labelField, $additional);
    }
}
