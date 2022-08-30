<?php
namespace Designnbuy\CustomerPhotoAlbum\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\AbstractModel;

class Album extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    )
    {
        parent::__construct($context);
    }

    protected function _construct()
    {
        $this->_init('designnbuy_customer_album', 'album_id');
    }
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object)
    {
        if ($object->getId()) {
            $stores = $this->getStoreIds($object->getId());

            $object->setData('store_id', $stores);
        }

        //$this->_retriveImageParams($object);
        //$this->_insertValueToObject($object);

        return parent::_afterLoad($object);
    }
    
    protected function _afterSave(AbstractModel $object)
    {
        if($object->getCustomerId() == '999999999'){
            $oldStores = $this->getStoreIds($object->getAlbumId());
            $newStores = (array)$object->getStores();
            if (empty($newStores)) {
                $newStores = (array)0;
            }
            
            $table = $this->getTable('designnbuy_customer_album_store');
            $insert = array_diff($newStores, $oldStores);
            $delete = array_diff($oldStores, $newStores);
            
            if ($delete) {
                $where = ['album_id = ?' => (int)$object->getId(), 'store_id IN (?)' => $delete];

                $this->getConnection()->delete($table, $where);
            }

            if ($insert) {
                $data = [];

                foreach ($insert as $storeId) {
                    $data[] = ['album_id' => (int)$object->getId(), 'store_id' => (int)$storeId];
                }
                $this->getConnection()->insertMultiple($table, $data);
            }
        }

        return parent::_afterSave($object);
    }
    public function getStoreIds($albumId)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from(
            $this->getTable('designnbuy_customer_album_store'),
            'store_id'
        )->where(
            'album_id = ?',
            (int)$albumId
        );

        return $connection->fetchCol($select);
    }
}