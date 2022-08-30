<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\PrintingMethod\Model\ResourceModel;

/**
 * PrintingMethod category resource model
 */
class PrintingMethod extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
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
     * @var \Magento\Store\Model\StoreFactory
     */
    protected $_storeFactory;
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
        \Magento\Store\Model\StoreFactory $_storeFactory,
        $resourcePrefix = null
    ) {
        parent::__construct($context, $resourcePrefix);
        $this->_date = $date;
        $this->dateTime = $dateTime;
        $this->string = $string;
        $this->_storeFactory = $_storeFactory;
    }

    /**
     * Initialize resource model
     * Get tablename from config
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('designnbuy_printingmethod_printingmethod', 'printingmethod_id');
    }

    /**
     * Process printingmethod data before deleting
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _beforeDelete(
        \Magento\Framework\Model\AbstractModel $object
    ){
        $condition = ['printingmethod_id = ?' => (int)$object->getId()];
        $tableSufixs = [
            'store',
            //'category',
            //'tag',
            'relatedproduct',
            //'relatedprintingmethod',
            //'relatedprintingmethod',
        ];
        foreach ($tableSufixs as $sufix) {
            $this->getConnection()->delete(
                $this->getTable('designnbuy_printingmethod_printingmethod_' . $sufix),
                ($sufix == 'relatedprintingmethod')
                    ? ['related_id = ?' => (int)$object->getId()]
                    : $condition
            );
        }

        return parent::_beforeDelete($object);
    }

    /**
     * Process printingmethod data before saving
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

        if (!$object->getPublishTime()) {
            $object->setPublishTime($object->getCreationTime());
        }

        $object->setUpdateTime($gmtDate);

        return parent::_beforeSave($object);
    }

    /**
     * Assign printingmethod to store views, categories, related printingmethods, etc.
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {

        if ($object->hasStoreLabels()) {
            $this->saveStoreLabels($object->getId(), $object->getStoreLabels());
        }

        //if($object->hasColor()){
        $colorArray = [];
        if($object->getColor()){
            $colorArray = $object->getColor();
        }
        $colorArray = $object->getColor();
            $this->saveColor($object->getId(), $colorArray);
        //}

        /*if($object->hasQaprice()){
            $this->saveQaprice($object->getId(), $object->getQaprice());
        }*/

        $originalQAPricesValues = $this->getQAPrices($object->getId());
        $newQapriceValues = !empty($object->getQaprice()) ? $object->getQaprice() : [];
        if (is_array($newQapriceValues)) {
            if (isset($originalQAPricesValues)) {
                $newQapriceValues = $this->markRemovedValues($newQapriceValues, $originalQAPricesValues);
            }
        }
        $this->saveQaprice($object->getId(), $newQapriceValues);

        //if($object->hasQcprice()){
        //$this->saveQcprice($object->getId(), $object->getQcprice());
        $originalQCPricesValues = $this->getQCPrices($object->getId());
        $newQcpriceValues = !empty($object->getQcprice()) ? $object->getQcprice() : [];
        if (is_array($newQcpriceValues)) {
            if (isset($originalQCPricesValues)) {
                $newQcpriceValues = $this->markRemovedValues($newQcpriceValues, $originalQCPricesValues);
            }
        }
        $this->saveQcprice($object->getId(), $newQcpriceValues);
        //}

        //if($object->hasQprice()){
        $originalQPricesValues = $this->getQPrices($object->getId());
        $newQpriceValues = !empty($object->getQprice()) ? $object->getQprice() : [];
        if (is_array($newQpriceValues)) {
            if (isset($originalQPricesValues)) {
                $newQpriceValues = $this->markRemovedValues($newQpriceValues, $originalQPricesValues);
            }
        }
        $this->saveQprice($object->getId(), $newQpriceValues);
        //}

        $oldStoreIds = $this->lookupStoreIds($object->getId());
        $newStoreIds = (array)$object->getStoreIds();
        if (!$newStoreIds) {
            $newStoreIds = [0];
        }

        $this->_updateLinks($object, $newStoreIds, $oldStoreIds, 'designnbuy_printingmethod_printingmethod_store', 'store_id');


        $oldColorCategoryIds = $this->lookupColorCategoryIds($object->getId());
        $newColorCategoryIds = (array)$object->getCategories();
        /*if (!$newColorCategoryIds) {
            $newColorCategoryIds = [0];
        }*/

        $this->_updateLinks($object, $newColorCategoryIds, $oldColorCategoryIds, 'designnbuy_printingmethod_printingmethod_color_category', 'category_id');


        /* Save related printingmethod & product links */
        if ($links = $object->getData('links')) {

            if (is_array($links)) {
                //foreach (['printingmethod', 'product'] as $linkType) {
                foreach (['product'] as $linkType) {
                    if (isset($links[$linkType]) && is_array($links[$linkType])) {
                        $linksData = $links[$linkType];
                        $lookup = 'lookupRelated' . ucfirst($linkType) . 'Ids';
                        $oldIds = $this->$lookup($object->getId());
                        $this->_updateLinks(
                            $object,
                            array_keys($linksData),
                            $oldIds,
                            'designnbuy_printingmethod_printingmethod_related' . $linkType,
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
     * Mark original values for removal if they are absent among new values
     *
     * @param $newValues array
     * @param $originalValues \Magento\Catalog\Model\Product\Option\Value[]
     * @return array
     */
    protected function markRemovedValues($newValues, $originalValues)
    {
        $existingValuesIds = [];

        foreach ($newValues as $newValue) {
            if (array_key_exists('id', $newValue)) {
                $existingValuesIds[] = $newValue['id'];
            }
        }
        /** @var $originalValue \Magento\Catalog\Model\Product\Option\Value */
        foreach ($originalValues as $originalValue) {
            if (!in_array($originalValue['id'], $existingValuesIds)) {
                $originalValue['is_delete'] =  1;
                $newValues[] = $originalValue;
            }
        }

        return $newValues;
    }

    /**
     * Update printingmethod connections
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
            $where = ['printingmethod_id = ?' => (int)$object->getId(), $field.' IN (?)' => $delete];

            $this->getConnection()->delete($table, $where);
        }

        if ($insert) {
            $data = [];

            foreach ($insert as $id) {
                $id = (int)$id;
                $data[] = array_merge(['printingmethod_id' => (int)$object->getId(), $field => $id],
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

            $categories = $this->lookupColorCategoryIds($object->getId());
            $object->setCategories($categories);
        }

        return parent::_afterLoad($object);
    }

    /**
     * Check if printingmethod identifier exist for specific store
     * return printingmethod id if printingmethod exists
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
            ['cps' => $this->getTable('designnbuy_printingmethod_printingmethod_store')],
            'cp.printingmethod_id = cps.printingmethod_id',
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
     *  Check whether printingmethod identifier is numeric
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return bool
     */
    protected function isNumericPageIdentifier(\Magento\Framework\Model\AbstractModel $object)
    {
        return preg_match('/^[0-9]+$/', $object->getData('identifier'));
    }

    /**
     *  Check whether printingmethod identifier is valid
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return bool
     */
    protected function isValidPageIdentifier(\Magento\Framework\Model\AbstractModel $object)
    {
        return preg_match('/^[a-z0-9][a-z0-9_\/-]+(\.[a-z0-9_-]+)?$/', $object->getData('identifier'));
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $printingmethodId
     * @return array
     */
    public function lookupStoreIds($printingmethodId)
    {
        return $this->_lookupIds($printingmethodId, 'designnbuy_printingmethod_printingmethod_store', 'store_id');
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $printingmethodId
     * @return array
     */
    public function lookupColorCategoryIds($printingmethodId)
    {
        return $this->_lookupIds($printingmethodId, 'designnbuy_printingmethod_printingmethod_color_category', 'category_id');
    }

    /**
     * Get related product ids to which specified item is assigned
     *
     * @param int $printingmethodId
     * @return array
     */
    public function lookupRelatedProductIds($printingmethodId)
    {
        return $this->_lookupIds($printingmethodId, 'designnbuy_printingmethod_printingmethod_relatedproduct', 'related_id');
    }

    /**
     * Get ids to which specified item is assigned
     * @param  int $printingmethodId
     * @param  string $tableName
     * @param  string $field
     * @return array
     */
    protected function _lookupIds($printingmethodId, $tableName, $field)
    {
        $adapter = $this->getConnection();

        $select = $adapter->select()->from(
            $this->getTable($tableName),
            $field
        )->where(
            'printingmethod_id = ?',
            (int)$printingmethodId
        );

        return $adapter->fetchCol($select);
    }


    /**
     * Save printingmethod labels for different store views
     *
     * @param int $printingmethodId
     * @param array $labels
     * @throws \Exception
     * @return $this
     */
    public function saveStoreLabels($printingmethodId, $labels)
    {
        $deleteByStoreIds = [];
        $table = $this->getTable('designnbuy_printingmethod_printingmethod_label');
        $connection = $this->getConnection();

        $data = [];
        foreach ($labels as $storeId => $label) {
            if ($this->string->strlen($label)) {
                $data[] = ['printingmethod_id' => $printingmethodId, 'store_id' => $storeId, 'label' => $label];
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
                $connection->delete($table, ['printingmethod_id=?' => $printingmethodId, 'store_id IN (?)' => $deleteByStoreIds]);
            }
        } catch (\Exception $e) {
            $connection->rollback();
            throw $e;
        }
        $connection->commit();

        return $this;
    }

    /**
     * Get all existing printingmethod labels
     *
     * @param int $printingmethodId
     * @return array
     */
    public function getStoreLabels($printingmethodId)
    {
        $select = $this->getConnection()->select()->from(
            $this->getTable('designnbuy_printingmethod_printingmethod_label'),
            ['store_id', 'label']
        )->where(
            'printingmethod_id = :printingmethod_id'
        );
        
        return $this->getConnection()->fetchPairs($select, [':printingmethod_id' => $printingmethodId]);
    }

    /**
     * Get printingmethod label by specific store id
     *
     * @param int $printingmethodId
     * @param int $storeId
     * @return string
     */
    public function getStoreLabel($printingmethodId, $storeId)
    {
        $select = $this->getConnection()->select()->from(
            $this->getTable('designnbuy_printingmethod_printingmethod_label'),
            'label'
        )->where(
            'printingmethod_id = :printingmethod_id'
        )->where(
            'store_id IN(0, :store_id)'
        )->order(
            'store_id DESC'
        );
        return $this->getConnection()->fetchOne($select, [':printingmethod_id' => $printingmethodId, ':store_id' => $storeId]);
    }


    /**
     * Save printingmethod labels for different store views
     *
     * @param int $printingmethodId
     * @param array $labels
     * @throws \Exception
     * @return $this
     */
    public function saveColor($printingmethodId, $colors)
    {
        /* Save related colors*/
        //if ($colors) {
            //if (is_array($colors)) {
                $oldIds = array();
                $oldIds = $this->lookupRelatedColorIds($printingmethodId);
                $this->_updateColors(
                    $printingmethodId,
                    $colors,
                    $oldIds,
                    'color_id',
                    'designnbuy_printingmethod_printablecolors'
                );
            //}
        //}
    }

    /**
     * Save quantity_area_side_prices
     *
     * @param int $printingmethodId
     * @param array $qaPrices
     * @throws \Exception
     * @return $this
     */
    public function saveQaPrice($printingmethodId, $qaPrices)
    {
        $sides = \Designnbuy\Merchandise\Helper\Data::SIDES;

        $table = $this->getTable('designnbuy_printingmethod_quantity_area_price');
        $field = 'id';
        if ($qaPrices) {
            $data = [];
            foreach ($qaPrices as $qaPrice) {
                if (!isset($qaPrice['is_delete']) || $qaPrice['is_delete'] != '1') {
                    $data = [
                        'id' => (isset($qaPrice['id']) && is_array($qaPrice)) ? $qaPrice['id'] : '',
                        'printingmethod_id' => (int)$printingmethodId,
                        'quantityrange_id' => (int)$qaPrice['quantityrange_id'],
                        'squarearea_id' => (int)$qaPrice['squarearea_id'],
                        'position' => 0,
                    ];

                    if (!empty($data[$field])) {
                        $where = $this->getConnection()->quoteInto($field . ' = ?', $data[$field]);
                        $this->getConnection()->update($table, $data, $where);
                        $qaPriceId = $data[$field];
                    } else {
                        $this->getConnection()->insert($table, $data);
                        $qaPriceId = $this->getConnection()->lastInsertId($table);
                    }

                    $sidesPrices = [];
                    for ($i = 0; $i < $sides; $i++){
                        $sidesPrices[$i] = $qaPrice[$i];
                    }

                    $this->saveQtyAreaSidesPrices($qaPriceId, $sidesPrices);
                } else {
                    if(isset($qaPrice['id']) && $qaPrice['id'] != ''){
                        $where = ['printingmethod_id = ?' => (int)$printingmethodId ,'id = ?' => (int)$qaPrice['id']];
                        $this->getConnection()->delete($table, $where);
                    }
                }
            }
        }
    }

    /**
     * Save price for different sides
     *
     * @param int $qaPriceId
     * @param array $prices
     * @throws \Exception
     * @return $this
     */
    public function saveQtyAreaSidesPrices($qaPriceId, $prices)
    {
        $deleteBySideIds = [];
        $table = $this->getTable('designnbuy_printingmethod_quantity_area_price_side_price');
        $connection = $this->getConnection();

        $data = [];
        foreach ($prices as $sideId => $price) {
            /*if ($price) {
                $data[] = ['quantity_area_id' => $qaPriceId, 'side_id' => $sideId, 'price' => $price];
            } else {
                $deleteBySideIds[] = $sideId;
            }*/
            $data[] = ['quantity_area_id' => $qaPriceId, 'side_id' => $sideId, 'price' => $price];
            $deleteBySideIds[] = $sideId;
        }

        $connection->beginTransaction();
        try {
            if (!empty($deleteBySideIds)) {
                $connection->delete($table, ['quantity_area_id=?' => $qaPriceId, 'side_id IN (?)' => $deleteBySideIds]);
            }

            if (!empty($data)) {
                $connection->insertOnDuplicate($table, $data, ['quantity_area_id', 'side_id']);
            }

        } catch (\Exception $e) {
            $connection->rollback();
            throw $e;
        }
        $connection->commit();

        return $this;
    }

    /**
     * Save quantity_area_side_prices
     *
     * @param int $printingmethodId
     * @param array $qcPrices
     * @throws \Exception
     * @return $this
     */
    public function saveQcPrice($printingmethodId, $qcPrices)
    {
        $sides = \Designnbuy\Merchandise\Helper\Data::SIDES;

        $table = $this->getTable('designnbuy_printingmethod_quantity_color_price');
        $field = 'id';
        if ($qcPrices) {
            $data = [];
            foreach ($qcPrices as $qcPrice) {
                if (!isset($qcPrice['is_delete']) || $qcPrice['is_delete'] != '1') {
                    $data = [
                        'id' => (isset($qcPrice['id']) && is_array($qcPrice)) ? $qcPrice['id'] : '',
                        'printingmethod_id' => (int)$printingmethodId,
                        'quantityrange_id' => (int)$qcPrice['quantityrange_id'],
                        'colorcounter_id' => (int)$qcPrice['colorcounter_id'],
                        'position' => 0,
                    ];

                    if (!empty($data[$field])) {
                        $where = $this->getConnection()->quoteInto($field . ' = ?', $data[$field]);
                        $this->getConnection()->update($table, $data, $where);
                        $qcPriceId = $data[$field];
                    } else {
                        $this->getConnection()->insert($table, $data);
                        $qcPriceId = $this->getConnection()->lastInsertId($table);
                    }

                    $sidesPrices = [];
                    for ($i = 0; $i < $sides; $i++){
                        $sidesPrices[$i] = $qcPrice[$i];
                    }
                
                    $this->saveQtyColorSidesPrices($qcPriceId, $sidesPrices);
                } else {
                    if(isset($qcPrice['id']) && $qcPrice['id'] != ''){
                        $where = ['printingmethod_id = ?' => (int)$printingmethodId ,'id = ?' => (int)$qcPrice['id']];
                        $this->getConnection()->delete($table, $where);
                    }
                }
            }
        }
    }

    /**
     * Save price for different sides
     *
     * @param int $qcPriceId
     * @param array $prices
     * @throws \Exception
     * @return $this
     */
    public function saveQtyColorSidesPrices($qcPriceId, $prices)
    {
        $deleteBySideIds = [];
        $table = $this->getTable('designnbuy_printingmethod_quantity_color_price_side_price');
        $connection = $this->getConnection();

        $data = [];
        foreach ($prices as $sideId => $price) {
            /*if ($price) {
                $data[] = ['quantity_color_id' => $qcPriceId, 'side_id' => $sideId, 'price' => $price];
            } else {
                $deleteBySideIds[] = $sideId;
            }*/
            $deleteBySideIds[] = $sideId;
            $data[] = ['quantity_color_id' => $qcPriceId, 'side_id' => $sideId, 'price' => $price];
        }

        $connection->beginTransaction();
        try {
            if (!empty($deleteBySideIds)) {
                $connection->delete($table, ['quantity_color_id=?' => $qcPriceId, 'side_id IN (?)' => $deleteBySideIds]);
            }

            if (!empty($data)) {
                $connection->insertOnDuplicate($table, $data, ['quantity_color_id','side_id']);
            }


        } catch (\Exception $e) {
            $connection->rollback();
            throw $e;
        }
        $connection->commit();

        return $this;
    }

    /**
     * Save quantity_side_prices
     *
     * @param int $printingmethodId
     * @param array $qPrices
     * @throws \Exception
     * @return $this
     */
    public function saveQPrice($printingmethodId, $qPrices)
    {
        $sides = \Designnbuy\Merchandise\Helper\Data::SIDES;

        $table = $this->getTable('designnbuy_printingmethod_quantity_price');
        $field = 'id';
        if ($qPrices) {
            $data = [];
            foreach ($qPrices as $qPrice) {
                if (!isset($qPrice['is_delete']) || $qPrice['is_delete'] != '1') {
                    $data = [
                        'id' => (isset($qPrice['id']) && is_array($qPrice)) ? $qPrice['id'] : '',
                        'printingmethod_id' => (int)$printingmethodId,
                        'quantityrange_id' => (int)$qPrice['quantityrange_id'],
                        'position' => 0,
                    ];

                    if (!empty($data[$field])) {
                        $where = $this->getConnection()->quoteInto($field . ' = ?', $data[$field]);
                        $this->getConnection()->update($table, $data, $where);
                        $qPriceId = $data[$field];
                    } else {
                        $this->getConnection()->insert($table, $data);
                        $qPriceId = $this->getConnection()->lastInsertId($table);
                    }

                    $sidesPrices = [];
                    for ($i = 0; $i < $sides; $i++){
                        $sidesPrices[$i] = $qPrice[$i];
                    }

                    $this->saveQtySidesPrices($qPriceId, $sidesPrices);
                } else {
                    if(isset($qPrice['is_delete']) && $qPrice['id'] != ''){
                        $where = ['printingmethod_id = ?' => (int)$printingmethodId ,'id = ?' => (int)$qPrice['id']];
                        $this->getConnection()->delete($table, $where);
                    }
                }
            }
        }
    }

    /**
     * Save price for different sides
     *
     * @param int $qPriceId
     * @param array $prices
     * @throws \Exception
     * @return $this
     */
    public function saveQtySidesPrices($qPriceId, $prices)
    {
        $deleteBySideIds = [];
        $table = $this->getTable('designnbuy_printingmethod_quantity_price_side_price');
        $connection = $this->getConnection();

        $data = [];
        foreach ($prices as $sideId => $price) {
            /*if ($price) {
                $data[] = ['quantity_id' => $qPriceId, 'side_id' => $sideId, 'price' => $price];
            } else {
                $deleteBySideIds[] = $sideId;
            }*/
            $deleteBySideIds[] = $sideId;
            $data[] = ['quantity_id' => $qPriceId, 'side_id' => $sideId, 'price' => $price];
        }

        $connection->beginTransaction();
        try {
            if (!empty($deleteBySideIds)) {
                $connection->delete($table, ['quantity_id=?' => $qPriceId, 'side_id IN (?)' => $deleteBySideIds]);
            }

            if (!empty($data)) {
                $connection->insertOnDuplicate($table, $data, ['quantity_id','side_id']);
            }

        } catch (\Exception $e) {
            $connection->rollback();
            throw $e;
        }
        $connection->commit();

        return $this;
    }


    /**
     * Get related product ids to which specified item is assigned
     *
     * @param int $printingmethodId
     * @return array
     */
    public function lookupRelatedColorIds($printingmethodId)
    {
        return $this->_lookupIds($printingmethodId, 'designnbuy_printingmethod_printablecolors', 'color_id');
    }


    /**
     * Update printingmethod connections
     * @param  \Magento\Framework\Model\AbstractModel $object
     * @param  Array $newRelatedIds
     * @param  Array $oldRelatedIds
     * @param  String $tableName
     * @param  String  $field
     * @param  Array  $rowData
     * @return void
     */
    protected function _updateColors(
        $printingmethodId,
        $colors,
        $oldIds,
        $field,
        $tableName
    ) {

        $table = $this->getTable($tableName);
        $delete = $oldIds;
        
        if ($delete) {

            $where = ['printingmethod_id = ?' => (int)$printingmethodId, $field.' IN (?)' => $delete];

            $this->getConnection()->delete($table, $where);
        }
        if ($colors) {
            $data = [];

            foreach ($colors as $color) {

                if (!isset($color['is_delete']) || $color['is_delete'] != '1') {
                    $data = [
                        'printingmethod_id' => (int)$printingmethodId,
                        $field => (isset($color[$field]) && is_array($color)) ? $color[$field] : '',
                        'color_code' => $color['color_code'],
                        'position' => 0,
                    ];

                    $storesCache = [];
                    foreach ($this->getStores() as $store) {
                        if(array_key_exists($store->getId(), $color) && !empty($color[$store->getId()] )){
                            $storesCache[$store->getId()] = $color[$store->getId()];
                        }
                    }
                    /*if (!empty($data[$field])) {
                        $where = $this->getConnection()->quoteInto($field . ' = ?', $data[$field]);
                        $this->getConnection()->update($table, $data, $where);
                        $colorId = $data[$field];
                    } else {
                        $this->getConnection()->insert($table, $data);
                        $colorId = $this->getConnection()->lastInsertId($table);
                    }*/
                    $this->getConnection()->insert($table, $data);
                    $colorId = $this->getConnection()->lastInsertId($table);
                    $this->saveColorLabels($colorId, $storesCache);
                } else {
                    if(isset($color['color_id']) && $color['color_id'] != ''){
                        $where = ['printingmethod_id = ?' => (int)$printingmethodId ,'color_id = ?' => (int)$color['color_id']];
                        $this->getConnection()->delete($table, $where);
                    }
                }
            }
        }
    }

    /**
     * Save color labels for different store views
     *
     * @param int $colorId
     * @param array $labels
     * @throws \Exception
     * @return $this
     */
    public function saveColorLabels($colorId, $labels)
    {
        $deleteByStoreIds = [];
        $table = $this->getTable('designnbuy_printingmethod_printablecolors_label');
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
     * @return mixed
     */
    public function getStores()
    {
        $stores = $this->_storeFactory->create()->getResourceCollection()->setLoadDefault(true)->load();
        return $stores;
    }


    /**
     * Get all existing colors and labels
     *
     * @param int $printingmethodId
     * @return array
     */
    public function getColors($printingmethodId)
    {
        $connection = $this->getConnection();

        $select = $connection->select()
            ->from($this->getTable('designnbuy_printingmethod_printablecolors'))
            ->where("printingmethod_id = ?", $printingmethodId);

        $colorData = [];
        $colors = $connection->fetchAll($select);

        if(!empty($colors)){
            foreach ($colors as $key => $color){
                $storeLabels = $this->getColorLabels($color['color_id']);

                $colorData[] = array_merge($color, $storeLabels);
            }
        }

        return $colorData;
    }

    /**
     * Get all existing colors labels
     *
     * @param int $colorId
     * @return array
     */
    public function getColorLabels($colorId)
    {
        $select = $this->getConnection()->select()->from(
            $this->getTable('designnbuy_printingmethod_printablecolors_label'),
            ['store_id', 'label']
        )->where(
            'color_id = :color_id'
        );
        return $this->getConnection()->fetchPairs($select, [':color_id' => $colorId]);
    }

    /**
     * Get all existing colors and labels
     *
     * @param int $printingmethodId
     * @return array
     */
    public function getQAPrices($printingmethodId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getTable('designnbuy_printingmethod_quantity_area_price'))
            ->where("printingmethod_id = ?", $printingmethodId);

        $qapricesData = [];
        $qaprices = $connection->fetchAll($select);

        if(!empty($qaprices)){
            foreach ($qaprices as $key => $qaprice){

                $sidesPrices = $this->getQASidesPrices($qaprice['id']);
                $qapricesData[] = array_merge($qaprice, $sidesPrices);
            }
        }

        return $qapricesData;
    }

    /**
     * Get all existing colors labels
     *
     * @param int $colorId
     * @return array
     */
    public function getQASidesPrices($qapriceId)
    {
        $select = $this->getConnection()->select()->from(
            $this->getTable('designnbuy_printingmethod_quantity_area_price_side_price'),
            ['side_id', 'price']
        )->where(
            'quantity_area_id = :quantity_area_id'
        );
        return $this->getConnection()->fetchPairs($select, [':quantity_area_id' => $qapriceId]);
    }

    /**
     * Get all existing colors and labels
     *
     * @param int $printingmethodId
     * @return array
     */
    public function getQCPrices($printingmethodId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getTable('designnbuy_printingmethod_quantity_color_price'))
            ->where("printingmethod_id = ?", $printingmethodId);

        $qcpricesData = [];
        $qcprices = $connection->fetchAll($select);

        if(!empty($qcprices)){
            foreach ($qcprices as $key => $qcprice){

                $sidesPrices = $this->getQCSidesPrices($qcprice['id']);
                $qcpricesData[] = array_merge($qcprice, $sidesPrices);
            }
        }

        return $qcpricesData;
    }

    /**
     * Get all existing colors labels
     *
     * @param int $colorId
     * @return array
     */
    public function getQCSidesPrices($qcpriceId)
    {
        $select = $this->getConnection()->select()->from(
            $this->getTable('designnbuy_printingmethod_quantity_color_price_side_price'),
            ['side_id', 'price']
        )->where(
            'quantity_color_id = :quantity_color_id'
        );
        return $this->getConnection()->fetchPairs($select, [':quantity_color_id' => $qcpriceId]);
    }

    /**
     * Get all existing colors and labels
     *
     * @param int $printingmethodId
     * @return array
     */
    public function getQPrices($printingmethodId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getTable('designnbuy_printingmethod_quantity_price'))
            ->where("printingmethod_id = ?", $printingmethodId);

        $qpricesData = [];
        $qprices = $connection->fetchAll($select);

        if(!empty($qprices)){
            foreach ($qprices as $key => $qprice){

                $sidesPrices = $this->getQSidesPrices($qprice['id']);
                $qpricesData[] = array_merge($qprice, $sidesPrices);
            }
        }

        return $qpricesData;
    }

    /**
     * Get all existing colors labels
     *
     * @param int $colorId
     * @return array
     */
    public function getQSidesPrices($qpriceId)
    {
        $select = $this->getConnection()->select()->from(
            $this->getTable('designnbuy_printingmethod_quantity_price_side_price'),
            ['side_id', 'price']
        )->where(
            'quantity_id = :quantity_id'
        );
        return $this->getConnection()->fetchPairs($select, [':quantity_id' => $qpriceId]);
    }

    public function getPrintingMethodQPrice($printingMethodId, $qty, $side)
    {
        $adapter = $this->getConnection();
        $qPricePrintingMethodTable = $this->getTable('designnbuy_printingmethod_quantity_price');
        
        $qrangeTable = $this->getTable('designnbuy_printingmethod_quantityrange');
        $sidePriceTable = $this->getTable('designnbuy_printingmethod_quantity_price_side_price');
        $qPriceselect = $adapter->select()
            ->from(['main_table' => $qPricePrintingMethodTable], array('*'))
            ->where('printingmethod_id=?',$printingMethodId)
            ->where('qr.range_from<=?',$qty)
            ->where('qr.range_to>=?',$qty)
            ->join(array('qr'=>$qrangeTable), 'qr.quantityrange_id = main_table.quantityrange_id', array('qr.*'))
            ->join(array('side_price'=>$sidePriceTable), 'side_price.quantity_id = main_table.id AND side_price.side_id = '.$side, array('side_price.*'));

        $qPriceData = $adapter->fetchRow($qPriceselect);
        return $qPriceData;
    }

    public function getPrintingMethodQCPrice($printingMethodId, $totalColors, $qty, $side)
    {
        $adapter = $this->getConnection();
        $qcPricePrintingMethodTable = $this->getTable('designnbuy_printingmethod_quantity_color_price');
        $colorsTable = $this->getTable('designnbuy_printingmethod_colorcounter');
        $qrangeTable = $this->getTable('designnbuy_printingmethod_quantityrange');
        $sidePriceTable = $this->getTable('designnbuy_printingmethod_quantity_color_price_side_price');
        $qcPriceselect = $adapter->select()
            ->from(['main_table' => $qcPricePrintingMethodTable], array('*'))
            ->where('printingmethod_id=?',$printingMethodId)
            ->where('qr.range_from<=?',$qty)
            ->where('qr.range_to>=?',$qty)
            //->where('clrs.counter=?',$totalColors)
            ->where('clrs.range_from<=?',$totalColors)
            ->where('clrs.range_to>=?',$totalColors)
            ->join(array('qr'=>$qrangeTable), 'main_table.quantityrange_id = qr.quantityrange_id', array('qr.*'))
            ->join(array('clrs'=>$colorsTable), 'main_table.colorcounter_id = clrs.colorcounter_id', array('clrs.*'))
            ->join(array('side_price'=>$sidePriceTable), 'side_price.quantity_color_id = main_table.id AND side_price.side_id = '.$side, array('side_price.*'));
        
        $qcPriceData = $adapter->fetchRow($qcPriceselect);
        return $qcPriceData;
    }

    public function getPrintingMethodQAPrice($printingMethodId, $area, $qty, $side)
    {
        $adapter = $this->getConnection();
        $qcPricePrintingMethodTable = $this->getTable('designnbuy_printingmethod_quantity_area_price');
        $squareareaTable = $this->getTable('designnbuy_printingmethod_squarearea');
        $qrangeTable = $this->getTable('designnbuy_printingmethod_quantityrange');
        $sidePriceTable = $this->getTable('designnbuy_printingmethod_quantity_area_price_side_price');
        $qcPriceselect = $adapter->select()
            ->from(['main_table' => $qcPricePrintingMethodTable], array('*'))
            ->where('printingmethod_id=?',$printingMethodId)
            ->where('qr.range_from <= ?',$qty)
            ->where('qr.range_to >= ?',$qty)
            ->where('squarearea.area >= ?',$area)
            ->join(array('qr'=>$qrangeTable), 'main_table.quantityrange_id = qr.quantityrange_id', array('qr.*'))
            ->join(array('squarearea'=>$squareareaTable), 'main_table.squarearea_id = squarearea.squarearea_id', array('squarearea.*'))
            //->join(array('side_price'=>$sidePriceTable), 'side_price.quantity_area_id = squarearea.squarearea_id AND side_price.side_id = '.$side, array('side_price.*'));
            ->join(array('side_price'=>$sidePriceTable), 'main_table.id = side_price.quantity_area_id AND  side_price.side_id = '.$side, array('side_price.*'));

        $qcPriceData = $adapter->fetchRow($qcPriceselect);
        return $qcPriceData;
    }

}
