<?php
namespace Designnbuy\CustomerPhotoAlbum\Model\ResourceModel\Album;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	/**
     * Add store data flag
     * @var bool
     */
	protected $_addStoreDataFlag = false;
	
	protected function _construct()
	{
		$this->_init('Designnbuy\CustomerPhotoAlbum\Model\Album', 'Designnbuy\CustomerPhotoAlbum\Model\ResourceModel\Album');
        $this->_map['fields']['page_id'] = 'main_table.page_id';
	}

	/**
     * Initialize select
     *
     * @return $this
     */
    protected function _initSelect()
    {
		parent::_initSelect();
		if ($this->_addStoreDataFlag) {
			$this->getSelect()->join(
				['detail' => "designnbuy_customer_album_store"],
				'main_table.album_id = detail.album_id',
				['store_id']
			);
		}
        return $this;
	}
	
	public function load($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }
        //$this->_eventManager->dispatch('review_review_collection_load_before', ['collection' => $this]);
        parent::load($printQuery, $logQuery);
        if ($this->_addStoreDataFlag) {
            $this->_addStoreData();
        }
        return $this;
	}

	public function addStoreData()
    {
        $this->_addStoreDataFlag = true;
        return $this;
	}
	
	/**
     * Add store data
     *
     * @return void
     */
    protected function _addStoreData()
    {
		$storesToAlbum = [];
		
		$connection = $this->getConnection();
		

		$albumIds = $this->getColumnValues('album_id');
        $storesToReviews = [];
        if (count($albumIds) > 0) {
            $inCond = $connection->prepareSqlCondition('album_id', ['in' => $albumIds]);
            $select = $connection->select()->from("designnbuy_customer_album_store")->where($inCond);
			$result = $connection->fetchAll($select);
			
            foreach ($result as $row) {
                if (!isset($storesToReviews[$row['album_id']])) {
                    $storesToReviews[$row['album_id']] = [];
                }
                $storesToReviews[$row['album_id']][] = $row['store_id'];
            }
        }
		foreach ($this as $item) {
            if (isset($storesToReviews[$item->getAlbumId()])) {
                $item->setStoreId($storesToReviews[$item->getAlbumId()]);
            } else {
                $item->setStoreId([]);
            }
        }
    }
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field === 'store_id') {
            return $this->addStoreFilter($condition, false);
        }

        return parent::addFieldToFilter($field, $condition);
    }
    public function addStoreFilter($store, $withAdmin = true)
    { 
        if (!$this->getFlag('store_filter_added')) {
            $this->performAddStoreFilter($store, $withAdmin);
        }
        return $this;
    }
    protected function performAddStoreFilter($store, $withAdmin = true)
    {
        if ($store instanceof \Magento\Store\Model\Store) {
            $store = [$store->getId()];
        }

        if (!is_array($store)) {
            $store = [$store];
        }

        if ($withAdmin) {
            $store[] = \Magento\Store\Model\Store::DEFAULT_STORE_ID;
        }

        $this->addFilter('store_id', ['in' => $store], 'public');
    }
    protected function _renderFiltersBefore()
    {
        if ($this->getFilter('store_id')) {
            
            $this->getSelect()->join(
				['detail' => "designnbuy_customer_album_store"],
				'main_table.album_id = detail.album_id',
				['store_id']
			);
        }
        
    }
}	