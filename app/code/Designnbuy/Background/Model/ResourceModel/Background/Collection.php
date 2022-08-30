<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Background\Model\ResourceModel\Background;

/**
 * Background background collection
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
    //protected $_date;

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
       // \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        //$this->_date = $date;
        $this->_storeManager = $storeManager;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);


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
        $this->_init('Designnbuy\Background\Model\Background', 'Designnbuy\Background\Model\ResourceModel\Background');
        $this->_map['fields']['background_id'] = 'main_table.background_id';
        $this->_map['fields']['store'] = 'store_table.store_id';
        $this->_map['fields']['category'] = 'category_table.category_id';
        //$this->_map['fields']['tag'] = 'tag_table.tag_id';
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
     * @param array|int|\Designnbuy\Background\Model\Category  $category
     * @return $this
     */
    public function addCategoryFilter($category)
    {
        if (!$this->getFlag('category_filter_added')) {
            if ($category instanceof \Designnbuy\Background\Model\Category) {
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
            ['title', 'content_heading', 'content'],
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
     * @param array|int|\Designnbuy\Background\Model\Tag  $tag
     * @return $this
     */
    public function addTagFilter($tag)
    {
        if (!$this->getFlag('tag_filter_added')) {
            if ($tag instanceof \Designnbuy\Background\Model\Tag) {
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
     * @param array|int|\Designnbuy\Background\Model\Author  $author
     * @return $this
     */
    public function addAuthorFilter($author)
    {
        if (!$this->getFlag('author_filter_added')) {
            if ($author instanceof \Designnbuy\Background\Model\Author) {
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
     * Perform operations after collection load
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        $items = $this->getColumnValues('background_id');
        if (count($items)) {
            $connection = $this->getConnection();
            $tableName = $this->getTable('designnbuy_background_background_store');
            $select = $connection->select()
                ->from(['cps' => $tableName])
                ->where('cps.background_id IN (?)', $items);

            $result = [];
            foreach ($connection->fetchAll($select) as $item) {
                if (!isset($result[$item['background_id']])) {
                    $result[$item['background_id']] = [];
                }
                $result[$item['background_id']][] = $item['store_id'];
            }

            if ($result) {
                foreach ($this as $item) {
                    $backgroundId = $item->getData('background_id');
                    if (!isset($result[$backgroundId])) {
                        continue;
                    }
                    if ($result[$backgroundId] == 0) {
                        $stores = $this->_storeManager->getStores(false, true);
                        $storeId = current($stores)->getId();
                    } else {
                        $storeId = $result[$item->getData('background_id')];
                    }
                    $item->setData('_first_store_id', $storeId);
                    $item->setData('store_ids', $result[$backgroundId]);
                }
            }

            foreach ($this as $item) {
                if ($this->_storeId) {
                    $item->setStoreId($this->_storeId);
                }
            }

            $map = [
                'category' => 'categories',
                //'tag' => 'tags',
            ];

            foreach ($map as $key => $property) {
                $tableName = $this->getTable('designnbuy_background_background_' . $key);
                $select = $connection->select()
                    ->from(['cps' => $tableName])
                    ->where('cps.background_id IN (?)', $items);

                $result = $connection->fetchAll($select);
                if ($result) {
                    $data = [];
                    foreach($result as $item) {
                        $data[$item['background_id']][] = $item[$key . '_id'];
                    }

                    foreach ($this as $item) {
                        $backgroundId = $item->getData('background_id');
                        if (isset($data[$backgroundId])) {
                            $item->setData($property, $data[$backgroundId]);
                        }
                    }
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
        foreach(['store', 'category', 'tag'] as $key) {
            if ($this->getFilter($key)) {
                $this->getSelect()->join(
                    [$key.'_table' => $this->getTable('designnbuy_background_background_'.$key)],
                    'main_table.background_id = '.$key.'_table.background_id',
                    []
                )->group(
                    'main_table.background_id'
                );
            }
        }
        parent::_renderFiltersBefore();
    }

}
