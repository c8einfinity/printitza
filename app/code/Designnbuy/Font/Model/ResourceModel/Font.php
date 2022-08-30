<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Font\Model\ResourceModel;

/**
 * Font category resource model
 */
class Font extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
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
        $this->_init('designnbuy_font_font', 'font_id');
    }

    /**
     * Process font data before deleting
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _beforeDelete(
        \Magento\Framework\Model\AbstractModel $object
    ){
        $condition = ['font_id = ?' => (int)$object->getId()];
        $tableSufixs = [
            'store',
            'category',
            'relatedproduct',
            'relatedfont',
            'relatedfont',
        ];
        foreach ($tableSufixs as $sufix) {
            $this->getConnection()->delete(
                $this->getTable('designnbuy_font_font_' . $sufix),
                ($sufix == 'relatedfont')
                    ? ['related_id = ?' => (int)$object->getId()]
                    : $condition
            );
        }

        return parent::_beforeDelete($object);
    }

    /**
     * Process font data before saving
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {

        //if ($object->getId()) {
            $font = $object->getCollection()
                ->addFieldToFilter('font_id', array('neq' => $object->getFontId()))
                ->addFieldToFilter('title', $object->getTitle())
                ->setPageSize(1)
                ->getFirstItem();

            if ($font->getFontId()) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('The font is already exist.')
                );
            }
        //}

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
     * Assign font to store views, categories, related fonts, etc.
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

        $this->_updateLinks($object, $newIds, $oldIds, 'designnbuy_font_font_store', 'store_id');

        /* Save category  links */
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
                    'designnbuy_font_font_' . $linkType,
                    $linkType . '_id'
                );
            }
        }


        /* Save related font & product links */
        if ($links = $object->getData('links')) {
            if (is_array($links)) {
                foreach (['font', 'product'] as $linkType) {
                    if (isset($links[$linkType]) && is_array($links[$linkType])) {
                        $linksData = $links[$linkType];
                        $lookup = 'lookupRelated' . ucfirst($linkType) . 'Ids';
                        $oldIds = $this->$lookup($object->getId());
                        $this->_updateLinks(
                            $object,
                            array_keys($linksData),
                            $oldIds,
                            'designnbuy_font_font_related' . $linkType,
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
     * Update font connections
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
            $where = ['font_id = ?' => (int)$object->getId(), $field.' IN (?)' => $delete];

            $this->getConnection()->delete($table, $where);
        }

        if ($insert) {
            $data = [];

            foreach ($insert as $id) {
                $id = (int)$id;
                $data[] = array_merge(['font_id' => (int)$object->getId(), $field => $id],
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
        }

        return parent::_afterLoad($object);
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $fontId
     * @return array
     */
    public function lookupStoreIds($fontId)
    {
        return $this->_lookupIds($fontId, 'designnbuy_font_font_store', 'store_id');
    }

    /**
     * Get category ids to which specified item is assigned
     *
     * @param int $fontId
     * @return array
     */
    public function lookupCategoryIds($fontId)
    {
        return $this->_lookupIds($fontId, 'designnbuy_font_font_category', 'category_id');
    }


    /**
     * Get related font ids to which specified item is assigned
     *
     * @param int $fontId
     * @return array
     */
    public function lookupRelatedFontIds($fontId)
    {
        return $this->_lookupIds($fontId, 'designnbuy_font_font_relatedfont', 'related_id');
    }

    /**
     * Get related product ids to which specified item is assigned
     *
     * @param int $fontId
     * @return array
     */
    public function lookupRelatedProductIds($fontId)
    {
        return $this->_lookupIds($fontId, 'designnbuy_font_font_relatedproduct', 'related_id');
    }

    /**
     * Get ids to which specified item is assigned
     * @param  int $fontId
     * @param  string $tableName
     * @param  string $field
     * @return array
     */
    protected function _lookupIds($fontId, $tableName, $field)
    {
        $adapter = $this->getConnection();

        $select = $adapter->select()->from(
            $this->getTable($tableName),
            $field
        )->where(
            'font_id = ?',
            (int)$fontId
        );

        return $adapter->fetchCol($select);
    }

    /**
     * Save font labels for different store views
     *
     * @param int $fontId
     * @param array $labels
     * @throws \Exception
     * @return $this
     */
    public function saveStoreLabels($fontId, $labels)
    {
        $deleteByStoreIds = [];
        $table = $this->getTable('designnbuy_font_font_label');
        $connection = $this->getConnection();

        $data = [];
        foreach ($labels as $storeId => $label) {
            if ($this->string->strlen($label)) {
                $data[] = ['font_id' => $fontId, 'store_id' => $storeId, 'label' => $label];
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
                $connection->delete($table, ['font_id=?' => $fontId, 'store_id IN (?)' => $deleteByStoreIds]);
            }
        } catch (\Exception $e) {
            $connection->rollback();
            throw $e;
        }
        $connection->commit();

        return $this;
    }

    /**
     * Get all existing font labels
     *
     * @param int $fontId
     * @return array
     */
    public function getStoreLabels($fontId)
    {
        $select = $this->getConnection()->select()->from(
            $this->getTable('designnbuy_font_font_label'),
            ['store_id', 'label']
        )->where(
            'font_id = :font_id'
        );
        return $this->getConnection()->fetchPairs($select, [':font_id' => $fontId]);
    }

    /**
     * Get font label by specific store id
     *
     * @param int $fontId
     * @param int $storeId
     * @return string
     */
    public function getStoreLabel($fontId, $storeId)
    {
        $select = $this->getConnection()->select()->from(
            $this->getTable('designnbuy_font_font_label'),
            'label'
        )->where(
            'font_id = :font_id'
        )->where(
            'store_id IN(0, :store_id)'
        )->order(
            'store_id DESC'
        );
        return $this->getConnection()->fetchOne($select, [':font_id' => $fontId, ':store_id' => $storeId]);
    }

}
