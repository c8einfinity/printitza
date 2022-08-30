<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Clipart\Model\ResourceModel\Clipart;

/**
 * Clipart clipart collection
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
        $this->_init('Designnbuy\Clipart\Model\Clipart', 'Designnbuy\Clipart\Model\ResourceModel\Clipart');
        $this->_map['fields']['clipart_id'] = 'main_table.clipart_id';
        $this->_map['fields']['store'] = 'store_table.store_id';
        $this->_map['fields']['category'] = 'category_table.category_id';
        $this->_map['fields']['tag'] = 'tag_table.tag_id';
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
            return $this->addStoreFilter($condition, false);
        }

        if ($field === 'category' || $field === 'categories') {
            return $this->addCategoryFilter($condition);
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
     * Add category filter to collection
     * @param array|int|\Designnbuy\Clipart\Model\Category  $category
     * @return $this
     */
    public function addCategoryFilter($category)
    {
        if (!$this->getFlag('category_filter_added')) {
            if ($category instanceof \Designnbuy\Clipart\Model\Category) {
                $category = [$category->getId()];
            }

            if (!is_array($category)) {
                $category = [$category];
            }

            $this->addFilter('category', ['in' => $category], 'public');
        }
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
            //['title', 'content_heading', 'content'],
            ['title'],
            [
                ['like' => '%' . $term . '%'],
                ['like' => '%' . $term . '%'],
                ['like' => '% ' . $term . ' %']
            ]
        );
        return $this;
    }

    /**
     * Add tag filter to collection
     * @param array|int|\Designnbuy\Clipart\Model\Tag  $tag
     * @return $this
     */
    public function addTagFilter($tag)
    {
        if (!$this->getFlag('tag_filter_added')) {
            if ($tag instanceof \Designnbuy\Clipart\Model\Tag) {
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
     * @param array|int|\Designnbuy\Clipart\Model\Author  $author
     * @return $this
     */
    public function addAuthorFilter($author)
    {
        if (!$this->getFlag('author_filter_added')) {
            if ($author instanceof \Designnbuy\Clipart\Model\Author) {
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
            ->addFieldToFilter('is_active', 1);
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
     * Load data
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @return $this
     */
    public function load($printQuery = false, $logQuery = false)
    {
        if ($this->isLoaded()) {
            return $this;
        }
        $this->_eventManager->dispatch('collection_load_before', ['collection' => $this]);
        parent::load($printQuery, $logQuery);

        return $this;
    }

    protected function _beforeLoad()
    {
        return parent::_beforeLoad();
        if (count($this)) {
            //$this->_eventManager->dispatch('collection_load_before', ['collection' => $this]);
        }

    }

    /**
     * Perform operations after collection load
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        $items = $this->getColumnValues('clipart_id');

        if (count($items)) {
            $connection = $this->getConnection();
            $tableName = $this->getTable('designnbuy_clipart_clipart_store');
            $select = $connection->select()
                ->from(['cps' => $tableName])
                ->where('cps.clipart_id IN (?)', $items);

            $result = [];
            foreach ($connection->fetchAll($select) as $item) {
                if (!isset($result[$item['clipart_id']])) {
                    $result[$item['clipart_id']] = [];
                }
                $result[$item['clipart_id']][] = $item['store_id'];
            }

            if ($result) {
                foreach ($this as $item) {
                    $clipartId = $item->getData('clipart_id');
                    if (!isset($result[$clipartId])) {
                        continue;
                    }
                    if ($result[$clipartId] == 0) {
                        $stores = $this->_storeManager->getStores(false, true);
                        $storeId = current($stores)->getId();
                    } else {
                        $storeId = $result[$item->getData('clipart_id')];
                    }
                    $item->setData('_first_store_id', $storeId);
                    $item->setData('store_ids', $result[$clipartId]);
                }
            }

            foreach ($this as $item) {
                if ($this->_storeId) {
                    $item->setStoreId($this->_storeId);
                }
            }

            $map = [
                'category' => 'categories',
                'tag' => 'tags',
            ];

            foreach ($map as $key => $property) {
                $tableName = $this->getTable('designnbuy_clipart_clipart_' . $key);
                $select = $connection->select()
                    ->from(['cps' => $tableName])
                    ->where('cps.clipart_id IN (?)', $items);

                $result = $connection->fetchAll($select);
                if ($result) {
                    $data = [];
                    foreach($result as $item) {
                        $data[$item['clipart_id']][] = $item[$key . '_id'];
                    }

                    foreach ($this as $item) {
                        $clipartId = $item->getData('clipart_id');
                        if (isset($data[$clipartId])) {
                            $item->setData($property, $data[$clipartId]);
                        }
                    }
                }
            }
        }
        if (count($this)) {
            $this->_eventManager->dispatch('collection_load_after', ['collection' => $this]);
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
        foreach(['store', 'category', 'tag'] as $key) {
            if ($this->getFilter($key)) {
                $this->getSelect()->join(
                    [$key.'_table' => $this->getTable('designnbuy_clipart_clipart_'.$key)],
                    'main_table.clipart_id = '.$key.'_table.clipart_id',
                    []
                )->group(
                    'main_table.clipart_id'
                );
            }
        }

        parent::_renderFiltersBefore();
    }

}
