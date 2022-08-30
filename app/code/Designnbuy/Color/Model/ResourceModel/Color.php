<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Color\Model\ResourceModel;

/**
 * Color category resource model
 */
class Color extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
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
        $this->_init('designnbuy_color_color', 'color_id');
    }

    /**
     * Process color data before deleting
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _beforeDelete(
        \Magento\Framework\Model\AbstractModel $object
    ){
        $condition = ['color_id = ?' => (int)$object->getId()];
        $tableSufixs = [
            'store',
            'category',
            'tag',
            'relatedproduct',
            'relatedcolor',
            'relatedcolor',
        ];
        foreach ($tableSufixs as $sufix) {
            $this->getConnection()->delete(
                $this->getTable('designnbuy_color_color_' . $sufix),
                ($sufix == 'relatedcolor')
                    ? ['related_id = ?' => (int)$object->getId()]
                    : $condition
            );
        }

        return parent::_beforeDelete($object);
    }

    /**
     * Process color data before saving
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        foreach (['publish_time', 'custom_theme_from', 'custom_theme_to'] as $field) {
            $value = $object->getData($field) ?: null;
            $object->setData($field, $this->dateTime->formatDate($value));
        }

        $identifierGenerator = \Magento\Framework\App\ObjectManager::getInstance()
                ->create('Designnbuy\Color\Model\ResourceModel\PageIdentifierGenerator');
        $identifierGenerator->generate($object);

        if (!$this->isValidPageIdentifier($object)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('The color URL key contains capital letters or disallowed symbols.')
            );
        }

        if ($this->isNumericPageIdentifier($object)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('The color URL key cannot be made of only numbers.')
            );
        }

        $gmtDate = $this->_date->gmtDate();

        if ($object->isObjectNew() && !$object->getCreationTime()) {
            $object->setCreationTime($gmtDate);
        }

        if (!$object->getPublishTime()) {
            $object->setPublishTime($object->getCreationTime());
        }

        $object->setUpdateTime($gmtDate);

        return parent::_beforeSave($object);
    }

    /**
     * Assign color to store views, categories, related colors, etc.
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

        $this->_updateLinks($object, $newIds, $oldIds, 'designnbuy_color_color_store', 'store_id');

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
                    'designnbuy_color_color_' . $linkType,
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
            $this->_updateLinks($object, $newIds, $oldIds, 'designnbuy_color_color_tag', 'tag_id');
        }

        /* Save related color & product links */
        if ($links = $object->getData('links')) {
            if (is_array($links)) {
                foreach (['color', 'product'] as $linkType) {
                    if (!empty($links[$linkType]) && is_array($links[$linkType])) {
                        $linksData = $links[$linkType];
                        $lookup = 'lookupRelated' . ucfirst($linkType) . 'Ids';
                        $oldIds = $this->$lookup($object->getId());
                        $this->_updateLinks(
                            $object,
                            array_keys($linksData),
                            $oldIds,
                            'designnbuy_color_color_related' . $linkType,
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
     * Update color connections
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
            $where = ['color_id = ?' => (int)$object->getId(), $field.' IN (?)' => $delete];

            $this->getConnection()->delete($table, $where);
        }

        if ($insert) {
            $data = [];

            foreach ($insert as $id) {
                $id = (int)$id;
                $data[] = array_merge(['color_id' => (int)$object->getId(), $field => $id],
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
     * Check if color identifier exist for specific store
     * return color id if color exists
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
            ['cps' => $this->getTable('designnbuy_color_color_store')],
            'cp.color_id = cps.color_id',
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

    /**
     *  Check whether color identifier is numeric
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return bool
     */
    protected function isNumericPageIdentifier(\Magento\Framework\Model\AbstractModel $object)
    {
        return preg_match('/^[0-9]+$/', $object->getData('identifier'));
    }

    /**
     *  Check whether color identifier is valid
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return bool
     */
    protected function isValidPageIdentifier(\Magento\Framework\Model\AbstractModel $object)
    {
        return preg_match('/^[a-z0-9][a-z0-9_\/-]+(\.[a-z0-9_-]+)?$/', $object->getData('identifier'));
    }

    /**
     * Check if color identifier exist for specific store
     * return color id if color exists
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
        $select->reset(\Zend_Db_Select::COLUMNS)->columns('cp.color_id')->order('cps.store_id DESC')->limit(1);

        return $this->getConnection()->fetchOne($select);
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $colorId
     * @return array
     */
    public function lookupStoreIds($colorId)
    {
        return $this->_lookupIds($colorId, 'designnbuy_color_color_store', 'store_id');
    }

    /**
     * Get category ids to which specified item is assigned
     *
     * @param int $colorId
     * @return array
     */
    public function lookupCategoryIds($colorId)
    {
        return $this->_lookupIds($colorId, 'designnbuy_color_color_category', 'category_id');
    }

    /**
     * Get tag ids to which specified item is assigned
     *
     * @param int $colorId
     * @return array
     */
    public function lookupTagIds($colorId)
    {
        return $this->_lookupIds($colorId, 'designnbuy_color_color_tag', 'tag_id');
    }

    /**
     * Get related color ids to which specified item is assigned
     *
     * @param int $colorId
     * @return array
     */
    public function lookupRelatedColorIds($colorId)
    {
        return $this->_lookupIds($colorId, 'designnbuy_color_color_relatedcolor', 'related_id');
    }

    /**
     * Get related product ids to which specified item is assigned
     *
     * @param int $colorId
     * @return array
     */
    public function lookupRelatedProductIds($colorId)
    {
        return $this->_lookupIds($colorId, 'designnbuy_color_color_relatedproduct', 'related_id');
    }

    /**
     * Get ids to which specified item is assigned
     * @param  int $colorId
     * @param  string $tableName
     * @param  string $field
     * @return array
     */
    protected function _lookupIds($colorId, $tableName, $field)
    {
        $adapter = $this->getConnection();

        $select = $adapter->select()->from(
            $this->getTable($tableName),
            $field
        )->where(
            'color_id = ?',
            (int)$colorId
        );

        return $adapter->fetchCol($select);
    }

    /**
     * Save color labels for different store views
     *
     * @param int $colorId
     * @param array $labels
     * @throws \Exception
     * @return $this
     */
    public function saveStoreLabels($colorId, $labels)
    {
        $deleteByStoreIds = [];
        $table = $this->getTable('designnbuy_color_color_label');
        $connection = $this->getConnection();

        $data = [];
        foreach ($labels as $storeId => $label) {
            if ($this->string->strlen($label)) {
                $data[] = ['color_id' => $colorId, 'store_id' => $storeId, 'label' => $label];
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
                $connection->delete($table, ['color_id=?' => $colorId, 'store_id IN (?)' => $deleteByStoreIds]);
            }
        } catch (\Exception $e) {
            $connection->rollback();
            throw $e;
        }
        $connection->commit();

        return $this;
    }

    /**
     * Get all existing color labels
     *
     * @param int $colorId
     * @return array
     */
    public function getStoreLabels($colorId)
    {
        $select = $this->getConnection()->select()->from(
            $this->getTable('designnbuy_color_color_label'),
            ['store_id', 'label']
        )->where(
            'color_id = :color_id'
        );
        return $this->getConnection()->fetchPairs($select, [':color_id' => $colorId]);
    }

    /**
     * Get color label by specific store id
     *
     * @param int $colorId
     * @param int $storeId
     * @return string
     */
    public function getStoreLabel($colorId, $storeId)
    {
        $select = $this->getConnection()->select()->from(
            $this->getTable('designnbuy_color_color_label'),
            'label'
        )->where(
            'color_id = :color_id'
        )->where(
            'store_id IN(0, :store_id)'
        )->order(
            'store_id DESC'
        );
        return $this->getConnection()->fetchOne($select, [':color_id' => $colorId, ':store_id' => $storeId]);
    }

}
