<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Clipart\Model\ResourceModel;

/**
 * Clipart category resource model
 */
class Clipart extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
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
     * Get tablename from config
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('designnbuy_clipart_clipart', 'clipart_id');
    }

    /**
     * Process clipart data before deleting
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _beforeDelete(
        \Magento\Framework\Model\AbstractModel $object
    ){
        $condition = ['clipart_id = ?' => (int)$object->getId()];
        $tableSufixs = [
            'store',
            'category',
            'tag',
            'relatedproduct',
            'relatedclipart',
            'relatedclipart',
        ];
        foreach ($tableSufixs as $sufix) {
            $this->getConnection()->delete(
                $this->getTable('designnbuy_clipart_clipart_' . $sufix),
                ($sufix == 'relatedclipart')
                    ? ['related_id = ?' => (int)$object->getId()]
                    : $condition
            );
        }

        return parent::_beforeDelete($object);
    }

    /**
     * Process clipart data before saving
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {

        $gmtDate = $this->_date->gmtDate();

        if ($object->isObjectNew() && !$object->getCreationTime()) {
            $object->setCreationTime($gmtDate);
        }

        $object->setUpdateTime($gmtDate);

        return parent::_beforeSave($object);
    }

    /**
     * Assign clipart to store views, categories, related cliparts, etc.
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->hasStoreLabels()) {
            $this->saveStoreLabels($object->getId(), $object->getStoreLabels());
        }

        $oldIds = $this->lookupStoreIds($object->getId());
        $newIds = (array)$object->getStoreIds();
        if (!$newIds) {
            $newIds = [0];
        }

        $this->_updateLinks($object, $newIds, $oldIds, 'designnbuy_clipart_clipart_store', 'store_id');

        /* Save category & tag links */
        foreach (['category' => 'categories', 'tag' => 'tags'] as $linkType => $dataKey) {
            $newIds = (array)$object->getData($dataKey);
            foreach($newIds as $key => $id) {
                if (!$id) { // e.g.: zero
                    unset($newIds[$key]);
                }
            }
            if (is_array($newIds)) {
                $lookup = 'lookup' . ucfirst($linkType) . 'Ids';
                $oldIds = $this->$lookup($object->getId());
                $this->_updateLinks(
                    $object,
                    $newIds,
                    $oldIds,
                    'designnbuy_clipart_clipart_' . $linkType,
                    $linkType . '_id'
                );
            }
        }

        /* Save tags links */
        $newIds = (array)$object->getTags();
        foreach($newIds as $key => $id) {
            if (!$id) { // e.g.: zero
                unset($newIds[$key]);
            }
        }
        if (is_array($newIds)) {
            $oldIds = $this->lookupTagIds($object->getId());
            $this->_updateLinks($object, $newIds, $oldIds, 'designnbuy_clipart_clipart_tag', 'tag_id');
        }

        /* Save related clipart & product links */
        if ($links = $object->getData('links')) {
            if (is_array($links)) {
                foreach (['clipart', 'product'] as $linkType) {
                    if (!empty($links[$linkType]) && is_array($links[$linkType])) {
                        $linksData = $links[$linkType];
                        $lookup = 'lookupRelated' . ucfirst($linkType) . 'Ids';
                        $oldIds = $this->$lookup($object->getId());
                        $this->_updateLinks(
                            $object,
                            array_keys($linksData),
                            $oldIds,
                            'designnbuy_clipart_clipart_related' . $linkType,
                            'related_id',
                            $linksData
                        );
                    }
                }
            }
        }

        return parent::_afterSave($object);
    }

    /**
     * Update clipart connections
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
            $where = ['clipart_id = ?' => (int)$object->getId(), $field.' IN (?)' => $delete];

            $this->getConnection()->delete($table, $where);
        }

        if ($insert) {
            $data = [];

            foreach ($insert as $id) {
                $id = (int)$id;
                $data[] = array_merge(['clipart_id' => (int)$object->getId(), $field => $id],
                    (isset($rowData[$id]) && is_array($rowData[$id])) ? $rowData[$id] : []
                );
            }

            $this->getConnection()->insertMultiple($table, $data);
        }
    }

    /**
     * Load an object using 'identifier' field if there's no field specified and value is not numeric
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @param mixed $value
     * @param string $field
     * @return $this
     */
    public function load(\Magento\Framework\Model\AbstractModel $object, $value, $field = null)
    {
        if (!is_numeric($value) && is_null($field)) {
            $field = 'identifier';
        }

        return parent::load($object, $value, $field);
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

            $categories = $this->lookupCategoryIds($object->getId());
            $object->setCategories($categories);

            $tags = $this->lookupTagIds($object->getId());
            $object->setTags($tags);
        }

        return parent::_afterLoad($object);
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $clipartId
     * @return array
     */
    public function lookupStoreIds($clipartId)
    {
        return $this->_lookupIds($clipartId, 'designnbuy_clipart_clipart_store', 'store_id');
    }

    /**
     * Get category ids to which specified item is assigned
     *
     * @param int $clipartId
     * @return array
     */
    public function lookupCategoryIds($clipartId)
    {
        return $this->_lookupIds($clipartId, 'designnbuy_clipart_clipart_category', 'category_id');
    }

    /**
     * Get tag ids to which specified item is assigned
     *
     * @param int $clipartId
     * @return array
     */
    public function lookupTagIds($clipartId)
    {
        return $this->_lookupIds($clipartId, 'designnbuy_clipart_clipart_tag', 'tag_id');
    }

    /**
     * Get related clipart ids to which specified item is assigned
     *
     * @param int $clipartId
     * @return array
     */
    public function lookupRelatedClipartIds($clipartId)
    {
        return $this->_lookupIds($clipartId, 'designnbuy_clipart_clipart_relatedclipart', 'related_id');
    }

    /**
     * Get related product ids to which specified item is assigned
     *
     * @param int $clipartId
     * @return array
     */
    public function lookupRelatedProductIds($clipartId)
    {
        return $this->_lookupIds($clipartId, 'designnbuy_clipart_clipart_relatedproduct', 'related_id');
    }

    /**
     * Get ids to which specified item is assigned
     * @param  int $clipartId
     * @param  string $tableName
     * @param  string $field
     * @return array
     */
    protected function _lookupIds($clipartId, $tableName, $field)
    {
        $adapter = $this->getConnection();

        $select = $adapter->select()->from(
            $this->getTable($tableName),
            $field
        )->where(
            'clipart_id = ?',
            (int)$clipartId
        );

        return $adapter->fetchCol($select);
    }

    /**
     * Save clipart labels for different store views
     *
     * @param int $clipartId
     * @param array $labels
     * @throws \Exception
     * @return $this
     */
    public function saveStoreLabels($clipartId, $labels)
    {
        $deleteByStoreIds = [];
        $table = $this->getTable('designnbuy_clipart_clipart_label');
        $connection = $this->getConnection();

        $data = [];
        foreach ($labels as $storeId => $label) {
            if ($this->string->strlen($label)) {
                $data[] = ['clipart_id' => $clipartId, 'store_id' => $storeId, 'label' => $label];
            } else {
                $deleteByStoreIds[] = $storeId;
            }
        }

        $connection->beginTransaction();
        try {
            if (!empty($data)) {
                $connection->insertOnDuplicate($table, $data, ['label']);
            }

            if (!empty($deleteByStoreIds)) {
                $connection->delete($table, ['clipart_id=?' => $clipartId, 'store_id IN (?)' => $deleteByStoreIds]);
            }
        } catch (\Exception $e) {
            $connection->rollback();
            throw $e;
        }
        $connection->commit();

        return $this;
    }

    /**
     * Get all existing clipart labels
     *
     * @param int $clipartId
     * @return array
     */
    public function getStoreLabels($clipartId)
    {
        $select = $this->getConnection()->select()->from(
            $this->getTable('designnbuy_clipart_clipart_label'),
            ['store_id', 'label']
        )->where(
            'clipart_id = :clipart_id'
        );
        return $this->getConnection()->fetchPairs($select, [':clipart_id' => $clipartId]);
    }

    /**
     * Get clipart label by specific store id
     *
     * @param int $clipartId
     * @param int $storeId
     * @return string
     */
    public function getStoreLabel($clipartId, $storeId)
    {
        $select = $this->getConnection()->select()->from(
            $this->getTable('designnbuy_clipart_clipart_label'),
            'label'
        )->where(
            'clipart_id = :clipart_id'
        )->where(
            'store_id IN(0, :store_id)'
        )->order(
            'store_id DESC'
        );
        return $this->getConnection()->fetchOne($select, [':clipart_id' => $clipartId, ':store_id' => $storeId]);
    }

    /**
     * Check if clipart identifier exist for specific store
     * return clipart id if clipart exists
     *
     * @param string $identifier
     * @param int|array $storeId
     * @return int
     */
    public function checkIdentifier($identifier, $storeIds)
    {
        if (!is_array($storeIds)) {
            $storeIds = [$storeIds];
        }
        $storeIds[] = \Magento\Store\Model\Store::DEFAULT_STORE_ID;
        $select = $this->_getLoadByIdentifierSelect($identifier, $storeIds, 1);
        $select->reset(\Zend_Db_Select::COLUMNS)->columns('cp.clipart_id')->order('cps.store_id DESC')->limit(1);

        return $this->getConnection()->fetchOne($select);
    }

    /**
     * Check if clipart identifier exist for specific store
     * return clipart id if clipart exists
     *
     * @param string $identifier
     * @param int $storeId
     * @return int
     */
    protected function _getLoadByIdentifierSelect($identifier, $storeIds, $isActive = null)
    {
        $select = $this->getConnection()->select()->from(
            ['cp' => $this->getMainTable()]
        )->join(
            ['cps' => $this->getTable('designnbuy_clipart_clipart_store')],
            'cp.clipart_id = cps.clipart_id',
            []
        )->where(
            'cp.identifier = ?',
            $identifier
        )->where(
            'cps.store_id IN (?)',
            $storeIds
        );

        if (!is_null($isActive)) {
            $select->where('cp.is_active = ?', $isActive)
                ->where('cp.publish_time <= ?', $this->_date->gmtDate());
        }
        return $select;
    }

}
