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
 * Clipart model
 *
 * @method \Designnbuy\Clipart\Model\ResourceModel\Clipart _getResource()
 * @method \Designnbuy\Clipart\Model\ResourceModel\Clipart getResource()
 * @method int getStoreId()
 * @method $this setStoreId(int $value)
 * @method string getTitle()
 * @method $this setTitle(string $value)
 * @method string getMetaKeywords()
 * @method $this setMetaKeywords(string $value)
 * @method $this setMetaDescription(string $value)
 * @method string getIdentifier()
 * @method $this setIdentifier(string $value)
 * @method string getContent()
 * @method $this setContent(string $value)
 * @method string getContentHeading()
 * @method $this setContentHeading(string $value)
 */
class Clipart extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Cliparts's Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    /**
     * Gallery images separator
     */
    const GALLERY_IMAGES_SEPARATOR = ';';

    /**
     * Base media folder path
     */
    const BASE_MEDIA_PATH = 'designnbuy'. DIRECTORY_SEPARATOR .'clipart';

    const BASE_MEDIA_URL = 'designnbuy/clipart/';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'designnbuy_clipart_clipart';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'clipart_clipart';

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $filterProvider;

    /**
     * @var \Designnbuy\Clipart\Model\Url
     */
    protected $_url;

    /**
     * @var \Designnbuy\Clipart\Model\AuthorFactory
     */
    protected $_authorFactory;

    /**
     * @var \Designnbuy\Clipart\Model\ResourceModel\Category\CollectionFactory
     */
    protected $_categoryCollectionFactory;


    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var \Designnbuy\Clipart\Model\ResourceModel\Category\Collection
     */
    protected $_parentCategories;


    /**
     * @var \Designnbuy\Clipart\Model\ResourceModel\Clipart\Collection
     */
    protected $_relatedClipartsCollection;

    /**
     * @var \Designnbuy\Clipart\Model\ImageFactory
     */
    protected $imageFactory;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Designnbuy\Clipart\Model\Url $url
     * @param \Designnbuy\Clipart\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        Url $url,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Designnbuy\Clipart\Model\ImageFactory $imageFactory,
        \Designnbuy\Clipart\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->filterProvider = $filterProvider;
        $this->_url = $url;
        $this->_storeManager = $storeManager;
        $this->imageFactory = $imageFactory;
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_relatedClipartsCollection = clone($this->getCollection());
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Designnbuy\Clipart\Model\ResourceModel\Clipart');
    }

    /**
     * Retrieve model title
     * @param  boolean $plural
     * @return string
     */
    public function getOwnTitle($plural = false)
    {
        return $plural ? 'Cliparts' : 'Clipart';
    }

    /**
     * Retrieve true if clipart is active
     * @return boolean [description]
     */
    public function isActive()
    {
        return ($this->getStatus() == self::STATUS_ENABLED);
    }

    /**
     * Retrieve available clipart statuses
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_DISABLED => __('Disabled'), self::STATUS_ENABLED => __('Enabled')];
    }

    /**
     * Check if clipart identifier exist for specific store
     * return clipart id if clipart exists
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
     * Retrieve clipart url route path
     * @return string
     */
    public function getUrl()
    {
        return $this->_url->getUrlPath($this, URL::CONTROLLER_CLIPART);
    }

    /**
     * Retrieve clipart url
     * @return string
     */
    public function getClipartUrl()
    {
        if (!$this->hasData('clipart_url')) {
            $url = $this->_url->getUrl($this, URL::CONTROLLER_CLIPART);
            $this->setData('clipart_url', $url);
        }

        return $this->getData('clipart_url');
    }


    /**
     * Retrieve featured image url
     * @return string
     */
    public function getImage()
    {
        if (!$this->hasData('clipart_image')) {
            if ($file = $this->getData('image')) {
                $image = $this->_url->getMediaUrl($file);
            } else {
                $image = false;
            }
            $this->setData('clipart_image', $image);
        }

        return $this->getData('clipart_image');
    }

    /**
     * Set media gallery images url
     *
     * @param array $images
     * @return this
     */
    public function setGalleryImages(array $images)
    {
        $this->setData('media_gallery',
            implode(
                self::GALLERY_IMAGES_SEPARATOR,
                $images
            )
        );

        /* Reinit Media Gallery Images */
        $this->unsetData('gallery_images');
        $this->getGalleryImages();

        return $this;
    }

    /**
     * Retrieve media gallery images url
     * @return string
     */
    public function getGalleryImages()
    {
        if (!$this->hasData('gallery_images')) {
            $images = array();
            $gallery = explode(
                self::GALLERY_IMAGES_SEPARATOR,
                $this->getData('media_gallery')
            );
            if (!empty($gallery)) {
                foreach ($gallery as $file) {
                    if ($file) {
                        $images[] = $this->imageFactory->create()
                            ->setFile($file);
                    }
                }
            }
            $this->setData('gallery_images', $images);
        }

        return $this->getData('gallery_images');
    }

    /**
     * Retrieve first image url
     * @return string
     */
    public function getFirstImage()
    {
        if (!$this->hasData('first_image')) {
            $image = $this->getImage();
            if (!$image) {
                $content = $this->getFilteredContent();
                $match = null;
                preg_match('/<img.+src=[\'"](?P<src>.+?)[\'"].*>/i', $content, $match);
                if (!empty($match['src'])) {
                    $image = $match['src'];
                }
            }
            $this->setData('first_image', $image);
        }

        return $this->getData('first_image');
    }

    /**
     * Retrieve filtered content
     *
     * @return string
     */
    public function getFilteredContent()
    {
        $key = 'filtered_content';
        if (!$this->hasData($key)) {
            $content = $this->filterProvider->getPageFilter()->filter(
                $this->getContent()
            );
            $this->setData($key, $content);
        }
        return $this->getData($key);
    }

    /**
     * Retrieve short filtered content
     *
     * @return string
     */
    public function getShortFilteredContent()
    {
        $key = 'short_filtered_content';
        if (!$this->hasData($key)) {
            $content = $this->getFilteredContent();
            $pageBraker = '<!-- pagebreak -->';

            if ($p = mb_strpos($content, $pageBraker)) {
                $content = mb_substr($content, 0, $p);
                try {
                    libxml_use_internal_errors(true);
                    $dom = new \DOMDocument();
                    $dom->loadHTML('<?xml encoding="UTF-8">' . $content);
                    $body = $dom->getElementsByTagName('body');
                    if ( $body && $body->length > 0 ) {
                        $body = $body->item(0);
                        $_content = new \DOMDocument;
                        foreach ($body->childNodes as $child){
                            $_content->appendChild($_content->importNode($child, true));
                        }
                        $content = $_content->saveHTML();
                    }
                } catch (\Exception $e) {}
            }

            $this->setData($key, $content);
        }

        return $this->getData($key);;
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
     * Retrieve clipart parent categories
     * @return \Designnbuy\Clipart\Model\ResourceModel\Category\Collection
     */
    public function getParentCategories()
    {
        if (is_null($this->_parentCategories)) {
            $this->_parentCategories = $this->_categoryCollectionFactory->create()
                ->addFieldToFilter('category_id', ['in' => $this->getCategories()])
                ->addStoreFilter($this->getStoreId())
                ->addActiveFilter()
                ->setOrder('position');
        }

        return $this->_parentCategories;
    }

    /**
     * Retrieve clipart parent categories count
     * @return int
     */
    public function getCategoriesCount()
    {
        return count($this->getParentCategories());
    }


    /**
     * Retrieve clipart related cliparts
     * @return \Designnbuy\Clipart\Model\ResourceModel\Clipart\Collection
     */
    public function getRelatedCliparts()
    {
        if (!$this->hasData('related_cliparts')) {
            $collection = $this->_relatedClipartsCollection
                ->addFieldToFilter('clipart_id', ['neq' => $this->getId()])
                ->addStoreFilter($this->getStoreId());
            $collection->getSelect()->joinLeft(
                ['rl' => $this->getResource()->getTable('designnbuy_clipart_clipart_relatedclipart')],
                'main_table.clipart_id = rl.related_id',
                ['position']
            )->where(
                'rl.clipart_id = ?',
                $this->getId()
            );
            $this->setData('related_cliparts', $collection);
        }

        return $this->getData('related_cliparts');
    }

    /**
     * Retrieve clipart related products
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
                ['rl' => $this->getResource()->getTable('designnbuy_clipart_clipart_relatedproduct')],
                'e.entity_id = rl.related_id',
                ['position']
            )->where(
                'rl.clipart_id = ?',
                $this->getId()
            );

            $this->setData('related_products', $collection);
        }

        return $this->getData('related_products');
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
     * Retrieve clipart publish date using format
     * @param  string $format
     * @return string
     */
    public function getUpdateDate($format = 'Y-m-d H:i:s')
    {
        return \Designnbuy\Clipart\Helper\Data::getTranslatedDate(
            $format,
            $this->getData('update_time')
        );
    }

    /**
     * Temporary method to get images from some custom clipart version. Do not use this method.
     * @param  string $format
     * @return string
     */
    public function getClipartImage()
    {
        $image = $this->getData('image');
        if (!$image) {
            $image = $this->getData('clipart_image');
        }
        return $image;
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
            'short_filtered_content',
            'filtered_content',
            'first_image',
            'image',
            'clipart_url',
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
     * Duplicate clipart and return new object
     * @return self
     */
    public function duplicate()
    {
        $object = clone $this;
        $object
            ->unsetData('clipart_id')
            ->setTitle($object->getTitle() . ' (' . __('Duplicated') . ')')
            ->setData('is_active', 0);

        $relatedProductIds = $this->getRelatedProducts()->getAllIds();
        $relatedPclipartIds = $this->getRelatedCliparts()->getAllIds();

        $object->setData(
            'links',
            [
                'product' => array_combine($relatedProductIds, $relatedProductIds),
                'clipart' => array_combine($relatedPclipartIds, $relatedPclipartIds),
            ]
        );

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

    /**
     * Retrieve clipart related cliparts
     * @return \Designnbuy\Clipart\Model\ResourceModel\Clipart\Collection
     */
    public function getCategoryRelatedCliparts($categoryId = null, $productId = null, $query = null)
    {
        if (!$this->hasData('category_related_cliparts')) {
            $collection = $this->_relatedClipartsCollection
                ->addActiveFilter()
                ->setOrder('position','ASC');

            if(isset($categoryId) && $categoryId != ''){
                $collection->addCategoryFilter($categoryId);
            }  else {
                if(isset($productId) && $productId != ''){
                    $categoryCollection =  $this->_categoryCollectionFactory->create()
                        ->addActiveFilter()
                        ->addStoreFilter($this->getStoreId());
                    $categoryCollection->addProductFilter($productId);
                    if($categoryCollection->getSize() > 0){
                        $categoryId = $categoryCollection->getAllIds();
                        $collection->addCategoryFilter($categoryId);
                    }
                }
            }

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $state = $objectManager->get('\Magento\Framework\App\State');

            if($state->getAreaCode() == \Magento\Framework\App\Area::AREA_ADMINHTML){
                $storeId =  \Magento\Store\Model\Store::DEFAULT_STORE_ID;
            }else{
                $storeId =  $this->_storeManager->getStore()->getId();
            }
            $collection->addStoreFilter($storeId, true);

            if($query != ''){
                //$collection->addSearchFilter($query);
                $connection = $this->getResource()->getConnection();
                $select = $connection->select()->from(
                    $this->getResource()->getTable('designnbuy_clipart_tag'),
                    'tag_id'
                )->where('title LIKE ?', '%' . $query . '%');
                $result = $connection->fetchCol($select);
                $collection->addTagFilter($result);
            }

            $cliparts = [];
            foreach ($collection as $clipart) {
                $cliparts[] = [
                    'id' => $clipart->getId(),
                    'title' => $clipart->getStoreLabel($this->_storeManager->getStore()->getId()) ? $clipart->getStoreLabel($this->_storeManager->getStore()->getId()) : $clipart->getTitle(),
                    'svg' => $clipart->getImage(),
                ];
            }

            $this->setData('category_related_cliparts', $cliparts);
        }

        return $this->getData('category_related_cliparts');
    }
}
