<?php
namespace Designnbuy\Reseller\Model\ResourceModel;

/**
 * Reseller Productpool resource model
 */
class Productpool extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
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
        $resourcePrefix = null
    ) {
        parent::__construct($context, $resourcePrefix);
        $this->_date = $date;
        $this->dateTime = $dateTime;
    }

    /**
     * Initialize resource model
     * Get tablename from config
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('designnbuy_reseller_productpool', 'entity_id');
    }

    /**
     * Retrieve date object
     * @return \Magento\Framework\Stdlib\DateTime
     */
    public function getDate()
    {
        return $this->_date;
    }

    /**
     * Process productpool data before deleting
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */

    protected function _beforeDelete(
        \Magento\Framework\Model\AbstractModel $object
    ) {
        $condition = ['productpool_id = ?' => (int)$object->getId()];
        $this->getConnection()->delete($this->getTable('designnbuy_reseller_productpool_products'), $condition);
        return parent::_beforeDelete($object);
    }

    /**
     * Process productpool data before saving
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
     * Assign post to store views, categories, related posts, etc.
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $newIds = (array)$object->getStoreIds();
        if (!$newIds) {
            $newIds = [0];
        }

        /* Save produtpool product links */
        $links = $object->getData('links');
        if ($links = $object->getData('links')) 
        {
            if (is_array($links)) 
            {
                $linksData = $links['product'];
                $oldIds = $this->lookupProductpoolProductsIds($object->getId());
                $this->_updateLinks(
                    $object,
                    array_keys($linksData),
                    $oldIds,
                    'designnbuy_reseller_productpool_products',
                    'product_id',
                    $linksData
                );
            }
        }
        return parent::_afterSave($object);
    }

    protected function _updateLinks(
        \Magento\Framework\Model\AbstractModel $object,
        array $newRelatedIds,
        array $oldRelatedIds,
        $tableName,
        $field,
        $rowData = []
    ) {
        $table = $this->getTable($tableName);
        $insert = $newRelatedIds;
        $delete = $oldRelatedIds;

        
        if ($delete) {
            $where = ['productpool_id = ?' => (int)$object->getId(), $field.' IN (?)' => $delete];
            $this->getConnection()->delete($table, $where);
        }

        if ($insert) {
            $data = [];
            foreach ($insert as $id) {
                $id = (int)$id;
                $data[] = array_merge(
                    [
                        'productpool_id' => (int)$object->getId(), $field => $id],
                        (isset($rowData[$id]) && is_array($rowData[$id])) ? $rowData[$id] : []
                );
            }
            
            $this->getConnection()->insertMultiple($table, $data);
        }
    }

     /**
     * Get related product ids to which specified item is assigned
     *
     * @param int $productpoolId
     * @return array
     */
    public function lookupProductpoolProductsIds($productpoolId)
    {
        return $this->_lookupIds($productpoolId, 'designnbuy_reseller_productpool_products', 'product_id');
    }

    protected function _lookupIds($productpoolId, $tableName, $field)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()->from(
            $this->getTable($tableName),
            $field
        )->where(
            'productpool_id = ?',
            (int)$productpoolId
        );

        return $adapter->fetchCol($select);
    }
}
