<?php

namespace Designnbuy\Template\Model\ResourceModel\Template;

/**
 * Template resource collection
 *
 */
class Collection extends \Magento\Catalog\Model\ResourceModel\Collection\AbstractCollection
{
    /**
     * Template collection factory
     *
     * @var \Designnbuy\Template\Model\ResourceModel\Template\CollectionFactory
     */
    protected $_templateCollectionFactory;

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
     * @param \Designnbuy\Template\Model\ResourceModel\Template\CollectionFactory $templateCollectionFactory
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
        \Designnbuy\Template\Model\ResourceModel\Template\CollectionFactory $templateCollectionFactory,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null
    ) {
        $this->_templateCollectionFactory = $templateCollectionFactory;
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
        $this->_init('Designnbuy\Template\Model\Template', 'Designnbuy\Template\Model\ResourceModel\Template');
        //$this->_map['fields']['entity_id'] = 'main_table.entity_id';
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
                ['website_table' => $this->getTable('designnbuy_template_website')],
                'e.entity_id = website_table.template_id',
                []
            )->group(
                'e.entity_id'
            );
        }
        parent::_renderFiltersBefore();
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

    /**
     * Perform operations after collection load
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        $items = $this->getColumnValues('template_id');
        if (count($items)) {
            $connection = $this->getConnection();
            $tableName = $this->getTable('designnbuy_template_website');
            $select = $connection->select()
                ->from(['cps' => $tableName])
                ->where('cps.template_id IN (?)', $items);

            $result = $connection->fetchPairs($select);
            if ($result) {
                foreach ($this as $item) {
                    $printingmethodId = $item->getData('template_id');
                    if (!isset($result[$printingmethodId])) {
                        continue;
                    }
                    if ($result[$printingmethodId] == 0) {
                        $stores = $this->_storeManager->getStores(false, true);
                        $storeId = current($stores)->getId();
                    } else {
                        $storeId = $result[$item->getData('template_id')];
                    }
                    $item->setData('_first_store_id', $storeId);
                    $item->setData('website_ids', [$result[$printingmethodId]]);
                }
            }

            if ($this->_storeId) {
                foreach ($this as $item) {
                    $item->setStoreId($this->_storeId);
                }
            }

        }

        $this->_previewFlag = false;
        return parent::_afterLoad();
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
        /** @var \Designnbuy\Template\Model\ResourceModel\Template\Collection $collection */
        $collection = $this->_templateCollectionFactory->create();

        $collection
            ->addAttributeToSelect('title')
            ->addAttributeToSelect('category_id')
            ->load();

        $options = [];

        if ($addEmpty) {
            $options[] = ['label' => __('-- Please Select a Template --'), 'value' => ''];
        }
        foreach ($collection as $template) {
            $options[] = ['label' => $template->getTitle(), 'value' => $template->getId(), 'category_id' => $template->getCategoryId()];
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
     * @param array|int|\Designnbuy\Template\Model\Category  $category
     * @return $this
     */
    public function addCategoryFilter($category, $findset = false)
    {
        if (!$this->getFlag('category_filter_added')) {
            if ($category instanceof \Designnbuy\Template\Model\Category) {
                $this->category = $category;
                $categories = $category->getChildrenIds();

                $categories[] = $category->getId();
            } else {
                $categories = $category;
                if (!is_array($categories)) {
                    $categories = [$categories];
                }
            }
            if($findset){
                $categoryIds[] = array('finset'=> array(end($categories)));
                $this->addAttributeToFilter('category_id', [$categoryIds]);
            } else {
                $this->addAttributeToFilter('category_id', ['in' => $categories], 'public');
            }
            //echo $this->getSelect(); exit;
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
     * Add template to filter
     *
     * @param \Magento\Eav\Model\Entity\Attribute\AbstractAttribute|string $attribute
     * @param array $condition
     * @param string $joinType
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function addTemplateFilter()
    {
        $this->getSelect()->where('e.type_id=?','template');
        return $this;

    }

    /**
     * Add template to filter
     *
     * @param \Magento\Eav\Model\Entity\Attribute\AbstractAttribute|string $attribute
     * @param array $condition
     * @param string $joinType
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function addLayoutFilter()
    {
        $this->getSelect()->where('e.type_id=?','layout');
        return $this;

    }

    /**
     * add the product filter to collection
     *
     * @access public
     * @param mixed (Mage_Catalog_Model_Product|int) $product
     * @return Designnbuy_Templates_Model_Resource_Producttemplate_Collection
     * @author Ultimate Module Creator
     */
    public function addProductFilter($product)
    {
        if ($product instanceof \Magento\Catalog\Model\Product) {
            $product = $product->getId();
        }
        if (!isset($this->_joinedFields['product'])) {
            $this->getSelect()->join(
                array('related_product' => $this->getTable('designnbuy_template_relatedproduct')),
                'related_product.template_id = e.entity_id',
                array('position')
            );
            $this->getSelect()->where('related_product.related_id = ?', $product);
            $this->_joinedFields['product'] = true;
        }
        return $this;
    }

    /**
     * Processs adding product website names to result collection
     *
     * @return $this
     */
    public function addWebsiteNamesToResult()
    {
        $templateWebsites = [];
        foreach ($this as $template) {
            $templateWebsites[$template->getId()] = [];
        }

        $templateWebsiteTable = $this->getResource()->getTable('designnbuy_template_website');
        if (!empty($templateWebsites)) {
            $select = $this->getConnection()->select()->from(
                ['template_website' => $templateWebsiteTable]
            )->join(
                ['website' => $this->getResource()->getTable('store_website')],
                'website.website_id = template_website.website_id',
                ['name']
            )->where(
                'template_website.template_id IN (?)',
                array_keys($templateWebsites)
            )->where(
                'website.website_id > ?',
                0
            );

            $data = $this->getConnection()->fetchAll($select);

            foreach ($data as $row) {
                $templateWebsites[$row['template_id']][] = $row['website_id'];
            }
        }
        foreach ($this as $template) {
            if (isset($templateWebsites[$template->getId()])) {
                $template->setData('websites', $templateWebsites[$template->getId()]);
            }
        }

        return $this;
    }
}
