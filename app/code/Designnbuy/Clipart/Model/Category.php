<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Clipart\Model;

use Designnbuy\Clipart\Model\Url;

/**
 * Category model
 *
 * @method \Designnbuy\Clipart\Model\ResourceModel\Category _getResource()
 * @method \Designnbuy\Clipart\Model\ResourceModel\Category getResource()
 * @method int getStoreId()
 * @method $this setStoreId(int $value)
 * @method string getTitle()
 * @method $this setTitle(string $value)
 * @method string getMetaKeywords()
 * @method $this setMetaKeywords(string $value)
 * @method string getMetaDescription()
 * @method $this setMetaDescription(string $value)
 * @method string getIdentifier()
 * @method $this setIdentifier(string $value)
 */
class Category extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Category's Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'designnbuy_clipart_category';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'clipart_category';

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;

    /**
     * @var \Designnbuy\Clipart\Model\ResourceModel\Clipart\CollectionFactory
     */
    protected $clipartCollectionFactory;

    /**
     * @var \Designnbuy\Clipart\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var array
     */
    protected $_options;

    /**
     * @var array
     */
    protected $_childs;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    protected $_productId;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Designnbuy\Clipart\Model\Url $url
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        Url $url,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Designnbuy\Clipart\Model\ResourceModel\Clipart\CollectionFactory $clipartCollectionFactory,
        \Designnbuy\Clipart\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_url = $url;
        $this->_storeManager = $storeManager;
        $this->clipartCollectionFactory = $clipartCollectionFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Designnbuy\Clipart\Model\ResourceModel\Category');
    }

    /**
     * Retrieve model title
     * @param  boolean $plural
     * @return string
     */
    public function getOwnTitle($plural = false)
    {
        return $plural ? 'Clipart Categories' : 'Clipart Category';
    }

    /**
     * Retrieve true if category is active
     * @return boolean [description]
     */
    public function isActive()
    {
        return ($this->getStatus() == self::STATUS_ENABLED);
    }

    /**
     * Retrieve available category statuses
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_DISABLED => __('Disabled'), self::STATUS_ENABLED => __('Enabled')];
    }

    /**
     * Check if category identifier exist for specific store
     * return category id if category exists
     *
     * @param string $identifier
     * @param int $storeId
     * @return int
     */
    public function checkIdentifier($identifier, $storeId)
    {
        return $this->_getResource()->checkIdentifier($identifier, $storeId);
    }

    /**
     * Retrieve parent category ids
     * @return array
     */
    public function getParentIds()
    {
        $k = 'parent_ids';
        if (!$this->hasData($k)) {
            $this->setData($k,
                $this->getPath() ? explode('/', $this->getPath()) : []
            );
        }

        return $this->getData($k);
    }

    /**
     * Retrieve parent category id
     * @return array
     */
    public function getParentId()
    {
        $parentIds = $this->getParentIds();
        if ($parentIds) {
            return $parentIds[count($parentIds) - 1];
        }

        return 0;
    }

    /**
     * Retrieve parent category
     * @return self || false
     */
    public function getParentCategory()
    {
        $k = 'parent_category';
        if (!$this->hasData($k)) {

            if ($pId = $this->getParentId()) {
                $category = clone $this;
                $category->load($pId);

                if ($category->getId()) {
                    $this->setData($k, $category);
                }
            }
        }

        if ($category = $this->getData($k)) {
            if ($category->isVisibleOnStore($this->getStoreId())) {
                return $category;
            }
        }

        return false;
    }

    /**
     * Check if current category is parent category
     * @param  self  $category
     * @return boolean
     */
    public function isParent($category)
    {
        if (is_object($category)) {
            $category = $category->getId();
        }

        return in_array($category, $this->getParentIds());
    }

    /**
     * Retrieve children category ids
     * @return array
     */
    public function getChildrenIds()
    {
        $k = 'children_ids';
        if (!$this->hasData($k)) {

            $categories = \Magento\Framework\App\ObjectManager::getInstance()
                ->create($this->_collectionName);

            $ids = [];
            foreach($categories as $category) {
                if ($category->isParent($this)) {
                    $ids[] = $category->getId();
                }
            }

            $this->setData($k,
                $ids
            );
        }

        return $this->getData($k);
    }

    /**
     * Check if current category is child category
     * @param  self  $category
     * @return boolean
     */
    public function isChild($category)
    {
        return $category->isParent($this);
    }

    /**
     * Retrieve category depth level
     * @return int
     */
    public function getLevel()
    {
        return count($this->getParentIds());
    }

    /**
     * Retrieve catgegory url route path
     * @return string
     */
    public function getUrl()
    {
        return $this->_url->getUrlPath($this, URL::CONTROLLER_CATEGORY);
    }

    /**
     * Retrieve category url
     * @return string
     */
    public function getCategoryUrl()
    {
        return $this->_url->getUrl($this, URL::CONTROLLER_CATEGORY);
    }

    /**
     * Retrieve meta title
     * @return string
     */
    public function getMetaTitle()
    {
        $title = $this->getData('meta_title');
        if (!$title) {
            $title = $this->getData('title');
        }

        return trim($title);
    }

    /**
     * Retrieve meta description
     * @return string
     */
    public function getMetaDescription()
    {
        $desc = $this->getData('meta_description');
        if (!$desc) {
            $desc = $this->getData('content');
        }

        $desc = strip_tags($desc);
        if (mb_strlen($desc) > 160) {
            $desc = mb_substr($desc, 0, 160);
        }

        return trim($desc);
    }

    /**
     * Retrieve if is visible on store
     * @return bool
     */
    public function isVisibleOnStore($storeId)
    {
        return $this->getIsActive() && array_intersect([0, $storeId], $this->getStoreIds());
    }

    /**
     * Retrieve number of cliparts in this category
     *
     * @return int
     */
    public function getClipartsCount()
    {
        $key = 'cliparts_count';
        if (!$this->hasData($key)) {

            $categories = $this->getChildrenIds();
            $categories[] = $this->getId();

            $cliparts = $this->clipartCollectionFactory->create()
                ->addActiveFilter()
                ->addStoreFilter($this->getStoreId())
                ->addCategoryFilter($categories);



            $this->setData($key, (int)$cliparts->getSize());
        }

        return $this->getData($key);
    }

    /**
     * Prepare all additional data
     * @param  string $format
     * @return self
     */
    public function initDinamicData()
    {
        $keys = [
            'meta_description',
            'meta_title',
        ];

        foreach ($keys as $key) {
            $method = 'get' . str_replace('_', '',
                    ucwords($key, '_')
                );
            $this->$method();
        }

        return $this;
    }

    /**
     * Duplicate category and return new object
     * @return self
     */
    public function duplicate()
    {
        $object = clone $this;
        $object
            ->unsetData('category_id')
            ->setTitle($object->getTitle() . ' (' . __('Duplicated') . ')')
            ->setData('is_active', 0);

        return $object->save();
    }

    /**
     * Get Category label by specified store
     *
     * @param \Magento\Store\Model\Store|int|bool|null $store
     * @return string|bool
     */
    public function getStoreLabel($store = null)
    {
        $storeId = $this->_storeManager->getStore($store)->getId();
        $labels = (array)$this->getStoreLabels();

        if (isset($labels[$storeId])) {
            return $labels[$storeId];
        } elseif (isset($labels[0]) && $labels[0]) {
            return $labels[0];
        } elseif ($store){
            return $this->getTitle();
        }

        return false;
    }

    /**
     * Set if not yet and retrieve category store labels
     *
     * @return array
     */
    public function getStoreLabels()
    {
        if (!$this->hasStoreLabels()) {
            $labels = $this->_getResource()->getStoreLabels($this->getId());
            $this->setStoreLabels($labels);
        }

        return $this->_getData('store_labels');
    }

    /**
     * Initialize rule model data from array.
     * Set store labels if applicable.
     *
     * @param array $data
     * @return $this
     */
    public function loadPost(array $data)
    {
        parent::loadPost($data);

        if (isset($data['store_labels'])) {
            $this->setStoreLabels($data['store_labels']);
        }

        return $this;
    }

    public function getCategoryTree($productId = null)
    {
        $this->_productId = $productId;
        return $catgories = $this->toOptionArray();
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->_options === null) {
            $this->_options = $this->_getOptions();
        }
        return $this->_options;
    }

    protected function _getOptions($itemId = 0)
    {
        $childs =  $this->_getChilds();
        $options = [];

        if (isset($childs[$itemId])) {
            foreach ($childs[$itemId] as $item) {
                $data = [
                    //'title' => $item->getTitle(),
                    'title' => $item->getStoreLabel($this->_storeManager->getStore()->getId()),
                    'id' => $item->getId(),
                ];
                if (isset($childs[$item->getId()])) {
                    $data['optgroup'] = $this->_getOptions($item->getId());
                }

                $options[] = $data;
            }
        }

        return $options;
    }

    protected function _getChilds()
    {
        $this->_childs = null;
        if ($this->_childs === null) {
            $collection =  $this->categoryCollectionFactory->create();
            $collection->addActiveFilter()->addStoreFilter($this->getStoreId());

            if($this->_productId != null){
                $productClipartCategoryCollection = clone $collection;
                $productClipartCategoryCollection->setOrder('related_product.position','ASC');
                $productClipartCategoryCollection->addProductFilter($this->_productId);
                if($productClipartCategoryCollection->getSize() == 0){
                    $productClipartCategoryCollection = clone $collection;
                }
            } else {
                $productClipartCategoryCollection = clone $collection;
            }
            $this->_childs = $productClipartCategoryCollection->getGroupedChilds();
        }

        return $this->_childs;
    }

    /**
     * Retrieve background related products
     * @return \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    public function getRelatedProducts()
    {
        if (!$this->hasData('related_products')) {
            $collection = $this->_productCollectionFactory->create();

            if ($this->getStoreId()) {
                $collection->addStoreFilter($this->getStoreId());
            }

            $collection->getSelect()->joinLeft(
                ['rl' => $this->getResource()->getTable('designnbuy_clipart_category_relatedproduct')],
                'e.entity_id = rl.related_id',
                ['position']
            )->where(
                'rl.category_id = ?',
                $this->getId()
            );

            $this->setData('related_products', $collection);
        }

        return $this->getData('related_products');
    }
}
