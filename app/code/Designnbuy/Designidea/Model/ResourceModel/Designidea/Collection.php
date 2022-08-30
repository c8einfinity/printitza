<?php

namespace Designnbuy\Designidea\Model\ResourceModel\Designidea;

/**
 * Designidea resource collection
 *
 */
class Collection extends \Magento\Catalog\Model\ResourceModel\Collection\AbstractCollection
{
    /**
     * Designidea collection factory
     *
     * @var \Designnbuy\Designidea\Model\ResourceModel\Designidea\CollectionFactory
     */
    protected $_designideaCollectionFactory;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Eav\Model\EntityFactory $eavEntityFactory
     * @param \Magento\Catalog\Model\ResourceModel\Helper $resourceHelper
     * @param \Magento\Framework\Validator\UniversalFactory $universalFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Designnbuy\Designidea\Model\ResourceModel\Designidea\CollectionFactory $designideaCollectionFactory
     * @param \Magento\Framework\DB\Adapter\AdapterInterface $connection
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Eav\Model\EntityFactory $eavEntityFactory,
        \Magento\Catalog\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\Validator\UniversalFactory $universalFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Designnbuy\Designidea\Model\ResourceModel\Designidea\CollectionFactory $designideaCollectionFactory,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null
    ) {
        $this->_designideaCollectionFactory = $designideaCollectionFactory;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $eavConfig,
            $resource,
            $eavEntityFactory,
            $resourceHelper,
            $universalFactory,
            $storeManager,
            $connection
        );
    }

    /**
     * Init collection and determine table names.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Designnbuy\Designidea\Model\Designidea', 'Designnbuy\Designidea\Model\ResourceModel\Designidea');
        $this->_map['fields']['id'] = 'main_table.entity_id';
        $this->_map['fields']['website'] = 'website_table.website_id';
    }

    /**
     * Join store relation table if there is store filter
     *
     * @return void
     */
    protected function _renderFiltersBefore()
    {
        if ($this->getFilter('website')) {
            $this->getSelect()->joinLeft(
                ['website_table' => $this->getTable('designnbuy_designidea_website')],
                'e.entity_id = website_table.designidea_id',
                []
            )->group(
                'e.entity_id'
            );
        }
        parent::_renderFiltersBefore();
    }

    /**
     * Add store filter to collection
     * @param array|int|\Magento\Store\Model\Store  $store
     * @param boolean $withAdmin
     * @return $this
     */
    public function addWebSiteFilter($website, $withAdmin = true)
    {
        if ($website === null) {
            return $this;
        }

        if (!$this->getFlag('website_filter_added')) {
            if ($website instanceof \Magento\Store\Model\Website) {
                $website = [$website->getId()];
            }

            if (!is_array($website)) {
                $website = [$website];
            }

            /*if (in_array(1, $website)) {
                return $this;
            }*/

            if ($withAdmin) {
                $website[] = 0;
            }

            $this->addFilter('website', ['in' => $website], 'public');
        }
        return $this;
    }

    /**
     * Convert items array to array for select options.
     *
     * @param $addEmpty bool
     * @return array
     */
    public function toOptionArray($addEmpty = true)
    {
        /** @var \Designnbuy\Designidea\Model\ResourceModel\Designidea\Collection $collection */
        $collection = $this->_designideaCollectionFactory->create();

        $collection
            ->addAttributeToSelect('title')
            ->addAttributeToSelect('category_id')
            ->load();

        $options = [];

        if ($addEmpty) {
            $options[] = ['label' => __('-- Please Select a Editable Artwork --'), 'value' => ''];
        }
        foreach ($collection as $designidea) {
            $options[] = ['label' => $designidea->getTitle(), 'value' => $designidea->getId(), 'category_id' => $designidea->getCategoryId()];
        }

        return $options;
    }

    /**
     * Add is_active filter to collection
     * @return $this
     */
    public function addActiveFilter()
    {
        return $this
            ->addFieldToFilter('status', 1);
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
     * @param array|int|\Designnbuy\Designidea\Model\Category  $category
     * @return $this
     */
    public function addCategoryFilter($category)
    {
        if (!$this->getFlag('category_filter_added')) {
            if ($category instanceof \Designnbuy\Designidea\Model\Category) {
                $this->category = $category;
                $categories = $category->getChildrenIds();
                $categories[] = $category->getId();
            } else {
                $categories = $category;
                if (!is_array($categories)) {
                    $categories = [$categories];
                }
            }



            //$this->addFilter('category_id', ['in' => $category], 'public');
            //$this->addAttributeToFilter('category_id', ['in' => $categories], 'public');
            $categoryIds = [];
            foreach ($categories as $categoryId) {
                $categoryIds[] = array('finset'=> array($categoryId));
            }

            $this->addFieldToFilter('category_id', [$categoryIds]);

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
        $collectionFilter[] = ['attribute' => 'title', 'like' => '%' . $term . '%'];
        $this->addAttributeToFilter($collectionFilter);
        /*$this->addFieldToFilter(
            ['attribute' => ['title']],
            [
                ['like' => '%' . $term . '%'],
                ['like' => '%' . $term . '%'],
                ['like' => '% ' . $term . ' %']
            ]
        );*/

        return $this;
    }

    /**
     * Add attribute to filter
     *
     * @param \Magento\Eav\Model\Entity\Attribute\AbstractAttribute|string $attribute
     * @param array $condition
     * @param string $joinType
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    /*public function addAttributeToFilter($attribute, $condition = null, $joinType = 'inner')
    {
        if ($attribute instanceof \Magento\Eav\Model\Entity\Attribute\AbstractAttribute) {
            $attribute = $attribute->getAttributeCode();
        }

        if (is_array($attribute)) {
            $sqlArr = [];
            foreach ($attribute as $condition) {
                $sqlArr[] = $this->_getAttributeConditionSql($condition['attribute'], $condition, $joinType);
            }
            $conditionSql = '(' . join(') OR (', $sqlArr) . ')';
            $this->getSelect()->where($conditionSql);
            return $this;
        }
        $this->addAttributeToSelect($attribute);
        $this->getSelect()->where($this->_getConditionSql('e.' . $attribute, $condition));

        return $this;

    }*/

    /**
     * Processs adding product website names to result collection
     *
     * @return $this
     */
    public function addWebsiteNamesToResult()
    {
        $designideaWebsites = [];
        foreach ($this as $designidea) {
            $designideaWebsites[$designidea->getId()] = [];
        }

        $designideaWebsiteTable = $this->getResource()->getTable('designnbuy_designidea_website');
        if (!empty($designideaWebsites)) {
            $select = $this->getConnection()->select()->from(
                ['designidea_website' => $designideaWebsiteTable]
            )->join(
                ['website' => $this->getResource()->getTable('store_website')],
                'website.website_id = designidea_website.website_id',
                ['name']
            )->where(
                'designidea_website.designidea_id IN (?)',
                array_keys($designideaWebsites)
            )->where(
                'website.website_id > ?',
                0
            );

            $data = $this->getConnection()->fetchAll($select);

            foreach ($data as $row) {
                $designideaWebsites[$row['designidea_id']][] = $row['website_id'];
            }
        }
        foreach ($this as $designidea) {
            if (isset($designideaWebsites[$designidea->getId()])) {
                $designidea->setData('websites', $designideaWebsites[$designidea->getId()]);
            }
        }

        return $this;
    }
}
