<?php
/**
 * Copyright © 2019 Designnbuy WebToPrint Pvt. Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 * ALWAYS DO BETTER @a
 */

namespace Drc\Storepickup\Model\ResourceModel;

class Holidays extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * Magento string lib
     *
     * @var \Magento\Framework\Stdlib\StringUtils
     */
    protected $string;

    /**
     * Construct
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param string|null $resourcePrefix
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Framework\Stdlib\StringUtils $string,
        $resourcePrefix = null
    ) {
        parent::__construct($context, $resourcePrefix);
        $this->_date = $date;
        $this->dateTime = $dateTime;
        $this->string = $string;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('drc_storepickup_holidays', 'entity_id');
    }

    /**
     * before save callback
     *
     * @param \Magento\Framework\Model\AbstractModel|\Drc\Storepickup\Model\Holidays $object
     * @return $this
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        return;
        $object->setFromDate($this->date->date());
        if ($object->isObjectNew()) {
            $object->setToDate($this->date->date());
        }
        return parent::_beforeSave($object);
    }

    /**
     * Assign color to store views, categories, related colors, etc.
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    public function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->hasStoreLabels()) {
            $this->saveStoreLabels($object->getId(), $object->getStoreLabels());
        }

        $oldIds = $this->lookupStoreIds($object->getId());
        $newIds = (array)$object->getStoreIds();
        if (!$newIds) {
            $newIds = [0];
        }
        
        $this->_updateLinks($object, $newIds, $oldIds, 'drc_storepickup_holidays_store', 'store_id');        
        return parent::_afterSave($object);
    }


    /**
     * Update holiday connections
     * @param  \Magento\Framework\Model\AbstractModel $object
     * @param  Array $newRelatedIds
     * @param  Array $oldRelatedIds
     * @param  String $tableName
     * @param  String  $field
     * @param  Array  $rowData
     * @return void
     */
    protected function _updateLinks(
        \Magento\Framework\Model\AbstractModel $object,
        Array $newRelatedIds,
        Array $oldRelatedIds,
        $tableName,
        $field,
        $rowData = []
    ) {
        $table = $this->getTable($tableName);

        $insert = $newRelatedIds;
        $delete = $oldRelatedIds;

        if ($delete) {
            $where = ['holidays_id = ?' => (int)$object->getId(), $field.' IN (?)' => $delete];
            $this->getConnection()->delete($table, $where);
        }

        if ($insert) {
            $data = [];
            foreach ($insert as $id) {
                $id = (int)$id;
                $data[] = array_merge(['holidays_id' => (int)$object->getId(), $field => $id],
                    (isset($rowData[$id]) && is_array($rowData[$id])) ? $rowData[$id] : []
                );
            }
            $this->getConnection()->insertMultiple($table, $data);
        }
    }


    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $holidaysId
     * @return array
     */
    public function lookupStoreIds($holidaysId)
    {
        return $this->_lookupIds($holidaysId, 'drc_storepickup_holidays_store', 'store_id');
    }

    /**
     * Get ids to which specified item is assigned
     * @param  int $holidaysId
     * @param  string $tableName
     * @param  string $field
     * @return array
     */
    protected function _lookupIds($holidaysId, $tableName, $field)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()->from(
            $this->getTable($tableName),
            $field
        )->where(
            'holidays_id = ?',
            (int)$holidaysId
        );

        return $adapter->fetchCol($select);
    }

    /**
     * Perform operations after object load
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getId()) {
            $storeIds = $this->lookupStoreIds($object->getId());
            $object->setData('store_ids', $storeIds);
        }

        return parent::_afterLoad($object);
    }

}
