<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Background\Model\ResourceModel;

/**
 * Background category resource model
 */
class Background extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
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
        $this->_init('designnbuy_background_background', 'background_id');
    }

    /**
     * Process background data before deleting
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _beforeDelete(
        \Magento\Framework\Model\AbstractModel $object
    ){
        $condition = ['background_id = ?' => (int)$object->getId()];
        $tableSufixs = [
            'store',
            'category',
            //'tag',
            'relatedproduct',
            'relatedbackground',
            'relatedbackground',
        ];
        foreach ($tableSufixs as $sufix) {
            $this->getConnection()->delete(
                $this->getTable('designnbuy_background_background_' . $sufix),
                ($sufix == 'relatedbackground')
                    ? ['related_id = ?' => (int)$object->getId()]
                    : $condition
            );
        }

        return parent::_beforeDelete($object);
    }

    /**
     * Process background data before saving
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
     * Assign background to store views, categories, related backgrounds, etc.
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

        $this->_updateLinks($object, $newIds, $oldIds, 'designnbuy_background_background_store', 'store_id');

        /* Save category & tag links */
        //foreach (['category' => 'categories', 'tag' => 'tags'] as $linkType => $dataKey) {
        foreach (['category' => 'categories'] as $linkType => $dataKey) {
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
                    'designnbuy_background_background_' . $linkType,
                    $linkType . '_id'
                );
            }
        }

        /* Save tags links */
        /*$newIds = (array)$object->getTags();
        foreach($newIds as $key => $id) {
            if (!$id) { // e.g.: zero
                unset($newIds[$key]);
            }
        }*/
        /*if (is_array($newIds)) {
            $oldIds = $this->lookupTagIds($object->getId());
            $this->_updateLinks($object, $newIds, $oldIds, 'designnbuy_background_background_tag', 'tag_id');
        }*/

        /* Save related background & product links */
        if ($links = $object->getData('links')) {
            if (is_array($links)) {
                foreach (['background', 'product'] as $linkType) {
                    if (isset($links[$linkType]) && is_array($links[$linkType])) {
                        $linksData = $links[$linkType];
                        $lookup = 'lookupRelated' . ucfirst($linkType) . 'Ids';
                        $oldIds = $this->$lookup($object->getId());
                        $this->_updateLinks(
                            $object,
                            array_keys($linksData),
                            $oldIds,
                            'designnbuy_background_background_related' . $linkType,
                            'related_id',
                            $linksData
                        );
                    }
                }
            }
        }

        /* Save related background images */
        if ($backgrounds = $object->getData('backgrounds')) {
            if (is_array($backgrounds)) {
                foreach (['backgrounds'] as $linkType) {
                    if (!empty($backgrounds) && is_array($backgrounds)) {
                        $oldIds = $this->lookupImageRelatedbackgroundsIds($object->getId());
                        $this->_updateImages(
                            $object,
                            array_keys($backgrounds),
                            $oldIds,
                            'designnbuy_background_background_images',
                            'image_id',
                            $backgrounds
                        );
                    }
                }
            }
        }

        return parent::_afterSave($object);
    }

    /**
     * Update background connections
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
            $where = ['background_id = ?' => (int)$object->getId(), $field.' IN (?)' => $delete];

            $this->getConnection()->delete($table, $where);
        }

        if ($insert) {
            $data = [];

            foreach ($insert as $id) {
                $id = (int)$id;
                $data[] = array_merge(['background_id' => (int)$object->getId(), $field => $id],
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

            /*$tags = $this->lookupTagIds($object->getId());
            $object->setTags($tags);*/
        }

        return parent::_afterLoad($object);
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $backgroundId
     * @return array
     */
    public function lookupStoreIds($backgroundId)
    {
        return $this->_lookupIds($backgroundId, 'designnbuy_background_background_store', 'store_id');
    }

    /**
     * Get category ids to which specified item is assigned
     *
     * @param int $backgroundId
     * @return array
     */
    public function lookupCategoryIds($backgroundId)
    {
        return $this->_lookupIds($backgroundId, 'designnbuy_background_background_category', 'category_id');
    }

    /**
     * Get tag ids to which specified item is assigned
     *
     * @param int $backgroundId
     * @return array
     */
    public function lookupTagIds($backgroundId)
    {
        return $this->_lookupIds($backgroundId, 'designnbuy_background_background_tag', 'tag_id');
    }

    /**
     * Get related background ids to which specified item is assigned
     *
     * @param int $backgroundId
     * @return array
     */
    public function lookupRelatedBackgroundIds($backgroundId)
    {
        return $this->_lookupIds($backgroundId, 'designnbuy_background_background_relatedbackground', 'related_id');
    }

    /**
     * Get related product ids to which specified item is assigned
     *
     * @param int $backgroundId
     * @return array
     */
    public function lookupRelatedProductIds($backgroundId)
    {
        return $this->_lookupIds($backgroundId, 'designnbuy_background_background_relatedproduct', 'related_id');
    }

    /**
     * Get ids to which specified item is assigned
     * @param  int $backgroundId
     * @param  string $tableName
     * @param  string $field
     * @return array
     */
    protected function _lookupIds($backgroundId, $tableName, $field)
    {
        $adapter = $this->getConnection();

        $select = $adapter->select()->from(
            $this->getTable($tableName),
            $field
        )->where(
            'background_id = ?',
            (int)$backgroundId
        );

        return $adapter->fetchCol($select);
    }

    /**
     * Save background labels for different store views
     *
     * @param int $backgroundId
     * @param array $labels
     * @throws \Exception
     * @return $this
     */
    public function saveStoreLabels($backgroundId, $labels)
    {
        $deleteByStoreIds = [];
        $table = $this->getTable('designnbuy_background_background_label');
        $connection = $this->getConnection();

        $data = [];
        foreach ($labels as $storeId => $label) {
            if ($this->string->strlen($label)) {
                $data[] = ['background_id' => $backgroundId, 'store_id' => $storeId, 'label' => $label];
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
                $connection->delete($table, ['background_id=?' => $backgroundId, 'store_id IN (?)' => $deleteByStoreIds]);
            }
        } catch (\Exception $e) {
            $connection->rollback();
            throw $e;
        }
        $connection->commit();

        return $this;
    }

    /**
     * Get all existing background labels
     *
     * @param int $backgroundId
     * @return array
     */
    public function getStoreLabels($backgroundId)
    {
        $select = $this->getConnection()->select()->from(
            $this->getTable('designnbuy_background_background_label'),
            ['store_id', 'label']
        )->where(
            'background_id = :background_id'
        );
        return $this->getConnection()->fetchPairs($select, [':background_id' => $backgroundId]);
    }

    /**
     * Get background label by specific store id
     *
     * @param int $backgroundId
     * @param int $storeId
     * @return string
     */
    public function getStoreLabel($backgroundId, $storeId)
    {
        $select = $this->getConnection()->select()->from(
            $this->getTable('designnbuy_background_background_label'),
            'label'
        )->where(
            'background_id = :background_id'
        )->where(
            'store_id IN(0, :store_id)'
        )->order(
            'store_id DESC'
        );
        return $this->getConnection()->fetchOne($select, [':background_id' => $backgroundId, ':store_id' => $storeId]);
    }

    /**
     * Update background images connections
     * @param  \Magento\Framework\Model\AbstractModel $object
     * @param  Array $newRelatedIds
     * @param  Array $oldRelatedIds
     * @param  String $tableName
     * @param  String  $field
     * @param  Array  $rowData
     * @return void
     */
    protected function _updateImages(
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
            $where = ['background_id = ?' => (int)$object->getId(), $field.' NOT IN (?)' => $newRelatedIds];
            $this->getConnection()->delete($table, $where);
        }

        if ($insert) {
            $data = [];
            foreach ($insert as $id) {
                $id = (int)$id;
                $data[] = array_merge(
                    ['background_id' => (int)$object->getId(), $field => $id],
                    (isset($rowData[$id]) && is_array($rowData[$id])) ? $rowData[$id] : []
                );
            }

            $this->getConnection()->insertOnDuplicate($table, $data);
        }
    }

    /**
     * Get related background ids to which specified item is assigned
     *
     * @param int $backgroundId
     * @return array
     */
    public function lookupImageRelatedBackgroundsIds($backgroundId)
    {
        return $this->_lookupImageIds($backgroundId, 'designnbuy_background_background_images', 'image_id');
    }

    /**
     * Get ids to which specified item is assigned
     * @param  int $backgroundId
     * @param  string $tableName
     * @param  string $field
     * @return array
     */
    protected function _lookupImageIds($backgroundId, $tableName, $field)
    {
        $adapter = $this->getConnection();

        $select = $adapter->select()->from(
            $this->getTable($tableName),
            $field
        )->where(
            'background_id = ?',
            (int)$backgroundId
        );
        return $adapter->fetchCol($select);
    }
}
