<?php
/**
 * Copyright © Designnbuy (support@designnbuy.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Designnbuy\Sheet\Model\ResourceModel\Size;

/**
 * Sheet size collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * @var int
     */
    protected $_storeId;


    /**
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param Magento\Store\Model\StoreManagerInterface $storeManager
     * @param null|\Zend_Db_Adapter_Abstract $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->_date = $date;
        $this->_storeManager = $storeManager;
    }

    /**
     * Constructor
     * Configures collection
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Designnbuy\Sheet\Model\Size', 'Designnbuy\Sheet\Model\ResourceModel\Size');
        $this->_map['fields']['size_id'] = 'main_table.size_id';
        $this->_map['fields']['store'] = 'store_table.store_id';
    }

    /**
     * Add field filter to collection
     *
     * @param string|array $field
     * @param null|string|array $condition
     * @return $this
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field === 'store_id' || $field === 'store_ids') {
            return $this->addStoreFilter($condition);
        }

        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Add store filter to collection
     * @param array|int|\Magento\Store\Model\Store  $store
     * @param boolean $withAdmin
     * @return $this
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        if ($store === null) {
            return $this;
        }

        if (!$this->getFlag('store_filter_added')) {
            if ($store instanceof \Magento\Store\Model\Store) {
                $this->_storeId = $store->getId();
                $store = [$store->getId()];
            }

            if (!is_array($store)) {
                $this->_storeId = $store;
                $store = [$store];
            }

            if (in_array(\Magento\Store\Model\Store::DEFAULT_STORE_ID, $store)) {
                return $this;
            }

            if ($withAdmin) {
                $store[] = \Magento\Store\Model\Store::DEFAULT_STORE_ID;
            }

            $this->addFilter('store', ['in' => $store], 'public');
        }
        return $this;
    }
    
    /**
     * Add "include in recent" filter to collection
     * @return $this
     */
    public function addRecentFilter()
    {
        return $this->addFieldToFilter('include_in_recent', 1);
    }
    
    /**
     * Add sizes filter to collection
     * @param array|int|string  $category
     * @return $this
     */
    public function addSizesFilter($sizeIds)
    {
        if (!is_array($sizeIds)) {
            $sizeIds = explode(',', $sizeIds);
            foreach ($sizeIds as $key => $id) {
                $id = trim($id);
                if (!$id) {
                    unset($sizeIds[$key]);
                }
            }
        }

        if (!count($sizeIds)) {
            $sizeIds = [0];
        }

        $this->addFieldToFilter(
            'size_id',
            ['in' => $sizeIds]
        );
    }


    /**
     * Add archive filter to collection
     * @param int $year
     * @param int $month
     * @return $this
     */
    public function addArchiveFilter($year, $month)
    {
        $this->getSelect()
            ->where('YEAR(publish_time) = ?', $year)
            ->where('MONTH(publish_time) = ?', $month);
        return $this;
    }

    /**
     * Add search filter to collection
     * @param string $term
     * @return $this
     */
    public function addSearchFilter($term)
    {
        $this->addFieldToFilter(
            ['title'],
            [
                ['like' => '%' . $term . '%'],
                ['like' => '%' . $term . '%'],
                ['like' => '%' . $term . '%']
            ]
        );

        return $this;
    }

    /**
     * Add tag filter to collection
     * @param array|int|\Designnbuy\Sheet\Model\Tag  $tag
     * @return $this
     */
    public function addTagFilter($tag)
    {
        if (!$this->getFlag('tag_filter_added')) {
            if ($tag instanceof \Designnbuy\Sheet\Model\Tag) {
                $tag = [$tag->getId()];
            }

            if (!is_array($tag)) {
                $tag = [$tag];
            }

            $this->addFilter('tag', ['in' => $tag], 'public');
        }
        return $this;
    }

    /**
     * Add author filter to collection
     * @param array|int|\Designnbuy\Sheet\Model\Author  $author
     * @return $this
     */
    public function addAuthorFilter($author)
    {
        if (!$this->getFlag('author_filter_added')) {
            if ($author instanceof \Designnbuy\Sheet\Model\Author) {
                $author = [$author->getId()];
            }

            if (!is_array($author)) {
                $author = [$author];
            }

            $this->addFilter('author_id', ['in' => $author], 'public');
        }
        return $this;
    }

    /**
     * Add is_active filter to collection
     * @return $this
     */
    public function addActiveFilter()
    {
        return $this
            ->addFieldToFilter('is_active', 1)
            ->addFieldToFilter('publish_time', ['lteq' => $this->_date->gmtDate()]);
    }

    /**
     * Get SQL for get record count
     *
     * Extra GROUP BY strip added.
     *
     * @return \Magento\Framework\DB\Select
     */
    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(\Magento\Framework\DB\Select::GROUP);

        return $countSelect;
    }

    /**
     * Perform operations after collection load
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        $items = $this->getColumnValues('size_id');
        if (count($items)) {
            $connection = $this->getConnection();
            $tableName = $this->getTable('designnbuy_sheet_size_store');
            $select = $connection->select()
                ->from(['cps' => $tableName])
                ->where('cps.size_id IN (?)', $items);

            $result = [];
            foreach ($connection->fetchAll($select) as $item) {
                if (!isset($result[$item['size_id']])) {
                    $result[$item['size_id']] = [];
                }
                $result[$item['size_id']][] = $item['store_id'];
            }

            if ($result) {
                foreach ($this as $item) {
                    $sizeId = $item->getData('size_id');
                    if (!isset($result[$sizeId])) {
                        continue;
                    }
                    if ($result[$sizeId] == 0) {
                        $stores = $this->_storeManager->getStores(false, true);
                        $storeId = current($stores)->getId();
                    } else {
                        $storeId = $result[$item->getData('size_id')];
                    }
                    $item->setData('_first_store_id', $storeId);
                    $item->setData('store_ids', $result[$sizeId]);
                }
            }

            foreach ($this as $item) {
                if ($this->_storeId) {
                    $item->setStoreId($this->_storeId);
                }

            }

        }

        $this->_previewFlag = false;
        return parent::_afterLoad();
    }

    /**
     * Join store relation table if there is store filter
     *
     * @return void
     */
    protected function _renderFiltersBefore()
    {
        foreach (['store'] as $key) {
            if ($this->getFilter($key)) {
                $joinOptions = new \Magento\Framework\DataObject;
                $joinOptions->setData([
                    'key' => $key,
                    'fields' => [],
                ]);
                $this->_eventManager->dispatch('mfsheet_size_collection_render_filter_join', ['join_options' => $joinOptions]);
                $this->getSelect()->join(
                    [$key.'_table' => $this->getTable('designnbuy_sheet_size_'.$key)],
                    'main_table.size_id = '.$key.'_table.size_id',
                    $joinOptions->getData('fields')
                )->group(
                    'main_table.size_id'
                );
            }
        }
        $wherePart = $this->getSelect()->getPart(\Magento\Framework\DB\Select::WHERE);
        foreach ($wherePart as $key => $cond) {
            if (strpos($cond, '`size`') !== false) {
                $sizeString = $this->get_string_between_size($cond, " '", "'");
                $widthHeight = explode(" X ", $sizeString);
                if(!empty($widthHeight)){
                    if(count($widthHeight) == 2){
                        $wherePart[$key] = "(`width` = '".$widthHeight[0]."' AND `height` = '".$widthHeight[1]."')";
                    }
                }
            } 
            
        }
        
        $this->getSelect()->setPart(\Magento\Framework\DB\Select::WHERE, $wherePart);
        
        parent::_renderFiltersBefore();
    }

    protected function get_string_between_size($string, $start, $end){
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }
    /**
     * Retrieve option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return parent::_toOptionArray('size_id', 'title');
    }
}
