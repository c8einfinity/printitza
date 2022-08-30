<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Background\Model;

use Designnbuy\Background\Model\Url;

/**
 * Background model
 *
 * @method \Designnbuy\Background\Model\ResourceModel\Background _getResource()
 * @method \Designnbuy\Background\Model\ResourceModel\Background getResource()
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
 * @method string getContent()
 * @method $this setContent(string $value)
 * @method string getContentHeading()
 * @method $this setContentHeading(string $value)
 */
class Background extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Backgrounds's Statuses
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
    const BASE_MEDIA_PATH = 'designnbuy'. DIRECTORY_SEPARATOR .'background';

    /**
     * Base media folder path
     */
    const BACKGROUND_IMAGES_MEDIA_PATH = 'designnbuy'. DIRECTORY_SEPARATOR .'background'. DIRECTORY_SEPARATOR .'images';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'designnbuy_background_background';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'background_background';

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $filterProvider;

    /**
     * @var \Designnbuy\Background\Model\Url
     */
    protected $_url;

    /**
     * @var \Designnbuy\Background\Model\AuthorFactory
     */
    protected $_authorFactory;

    /**
     * @var \Designnbuy\Background\Model\ResourceModel\Category\CollectionFactory
     */
    protected $_categoryCollectionFactory;

    /**
     * @var \Designnbuy\Background\Model\ResourceModel\Tag\CollectionFactory
     */
    protected $_tagCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var \Designnbuy\Background\Model\ResourceModel\Category\Collection
     */
    protected $_parentCategories;

    /**
     * @var \Designnbuy\Background\Model\ResourceModel\Tag\Collection
     */
    protected $_relatedTags;

    /**
     * @var \Designnbuy\Background\Model\ResourceModel\Background\Collection
     */
    protected $_relatedBackgroundsCollection;

    /**
     * @var \Designnbuy\Background\Model\ImageFactory
     */
    protected $imageFactory;

    /**
     * @var \Designnbuy\Background\Model\ResourceModel\Background\Product
     */
    protected $backgroundProduct;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    protected $helper;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Designnbuy\Background\Model\Url $url
     * @param \Designnbuy\Background\Model\AuthorFactory $authorFactory
     * @param \Designnbuy\Background\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \Designnbuy\Background\Model\ResourceModel\Tag\CollectionFactory $tagCollectionFactory
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
        \Designnbuy\Background\Helper\Data $helper,
        \Designnbuy\Background\Model\ImageFactory $imageFactory,
        \Designnbuy\Background\Model\AuthorFactory $authorFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Designnbuy\Background\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Designnbuy\Background\Model\ResourceModel\Tag\CollectionFactory $tagCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Designnbuy\Background\Model\ResourceModel\Background\Product $backgroundProduct,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->filterProvider = $filterProvider;
        $this->_url = $url;
        $this->helper = $helper;
        $this->imageFactory = $imageFactory;
        $this->_authorFactory = $authorFactory;
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
        $this->_tagCollectionFactory = $tagCollectionFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_relatedBackgroundsCollection = clone($this->getCollection());
        $this->backgroundProduct = $backgroundProduct;
        $this->_storeManager = $storeManager;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Designnbuy\Background\Model\ResourceModel\Background');
    }

    /**
     * Retrieve model title
     * @param  boolean $plural
     * @return string
     */
    public function getOwnTitle($plural = false)
    {
        return $plural ? 'Backgrounds' : 'Background';
    }

    /**
     * Retrieve true if background is active
     * @return boolean [description]
     */
    public function isActive()
    {
        return ($this->getStatus() == self::STATUS_ENABLED);
    }

    /**
     * Retrieve available background statuses
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_DISABLED => __('Disabled'), self::STATUS_ENABLED => __('Enabled')];
    }

    /**
     * Check if background identifier exist for specific store
     * return background id if background exists
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
     * Retrieve background url route path
     * @return string
     */
    public function getUrl()
    {
        return $this->_url->getUrlPath($this, URL::CONTROLLER_CLIPART);
    }

    /**
     * Retrieve background url
     * @return string
     */
    public function getBackgroundUrl()
    {
        if (!$this->hasData('background_url')) {
            $url = $this->_url->getUrl($this, URL::CONTROLLER_CLIPART);
            $this->setData('background_url', $url);
        }

        return $this->getData('background_url');
    }


    /**
     * Retrieve featured image url
     * @return string
     */
    public function getImage()
    {
        if (!$this->hasData('background_image')) {
            if ($file = $this->getData('image')) {
                $image = $this->_url->getBackgroundImageMediaUrl($file);
            } else {
                $image = false;
            }
            $this->setData('background_image', $image);
        }

        return $this->getData('background_image');
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
     * Retrieve background parent categories
     * @return \Designnbuy\Background\Model\ResourceModel\Category\Collection
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
     * Retrieve background parent categories count
     * @return int
     */
    public function getCategoriesCount()
    {
        return count($this->getParentCategories());
    }

    /**
     * Retrieve background tags
     * @return \Designnbuy\Background\Model\ResourceModel\Tag\Collection
     */
    public function getRelatedTags()
    {
        if (is_null($this->_relatedTags)) {
            $this->_relatedTags = $this->_tagCollectionFactory->create()
                ->addFieldToFilter('tag_id', ['in' => $this->getTags()])
                ->setOrder('title');
        }

        return $this->_relatedTags;
    }

    /**
     * Retrieve background tags count
     * @return int
     */
    public function getTagsCount()
    {
        return count($this->getRelatedTags());
    }

    /**
     * Retrieve background related backgrounds
     * @return \Designnbuy\Background\Model\ResourceModel\Background\Collection
     */
    public function getRelatedBackgrounds()
    {
        if (!$this->hasData('related_backgrounds')) {
            $collection = $this->_relatedBackgroundsCollection
                ->addFieldToFilter('background_id', ['neq' => $this->getId()])
                ->addStoreFilter($this->getStoreId());
            $collection->getSelect()->joinLeft(
                ['rl' => $this->getResource()->getTable('designnbuy_background_background_relatedbackground')],
                'main_table.background_id = rl.related_id',
                ['position']
            )->where(
                'rl.background_id = ?',
                $this->getId()
            );
            $this->setData('related_backgrounds', $collection);
        }

        return $this->getData('related_backgrounds');
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
                ['rl' => $this->getResource()->getTable('designnbuy_background_background_relatedproduct')],
                'e.entity_id = rl.related_id',
                ['position']
            )->where(
                'rl.background_id = ?',
                $this->getId()
            );

            $this->setData('related_products', $collection);
        }

        return $this->getData('related_products');
    }

    /**
     * Retrieve background author
     * @return \Designnbuy\Background\Model\Author | false
     */
    public function getAuthor()
    {
        if (!$this->hasData('author')) {
            $author = false;
            if ($authorId = $this->getData('author_id')) {
                $_author = $this->_authorFactory->create();
                $_author->load($authorId);
                if ($_author->getId()) {
                    $author = $_author;
                }
            }
            $this->setData('author', $author);
        }
        return $this->getData('author');
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
     * Retrieve background publish date using format
     * @param  string $format
     * @return string
     */
    public function getPublishDate($format = 'Y-m-d H:i:s')
    {
        return \Designnbuy\Background\Helper\Data::getTranslatedDate(
            $format,
            $this->getData('creation_time')
        );
    }

    /**
     * Retrieve background publish date using format
     * @param  string $format
     * @return string
     */
    public function getUpdateDate($format = 'Y-m-d H:i:s')
    {
        return \Designnbuy\Background\Helper\Data::getTranslatedDate(
            $format,
            $this->getData('update_time')
        );
    }

    /**
     * Temporary method to get images from some custom background version. Do not use this method.
     * @param  string $format
     * @return string
     */
    public function getBackgroundImage()
    {
        $image = $this->getData('image');
        if (!$image) {
            $image = $this->getData('background_image');
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
            'background_url',
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
     * Duplicate background and return new object
     * @return self
     */
    public function duplicate()
    {
        $object = clone $this;
        $object
            ->unsetData('background_id')
            ->setTitle($object->getTitle() . ' (' . __('Duplicated') . ')')
            ->setData('is_active', 0);

        $relatedProductIds = $this->getRelatedProducts()->getAllIds();
        $relatedPbackgroundIds = $this->getRelatedBackgrounds()->getAllIds();

        $object->setData(
            'links',
            [
                'product' => array_combine($relatedProductIds, $relatedProductIds),
                'background' => array_combine($relatedPbackgroundIds, $relatedPbackgroundIds),
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
     * Set media gallery images url
     *
     * @param array $images
     * @return this
     */
    public function setBackgroundImages(array $images)
    {
        $this->setData('media_gallery',
            implode(
                self::GALLERY_IMAGES_SEPARATOR,
                $images
            )
        );

        /* Reinit Media Gallery Images */
        $this->unsetData('gallery_images');
        $this->getBackgroundImages();

        return $this;
    }

    /**
     * Retrieve media gallery images url
     * @return string
     */
    public function getBackgroundImages()
    {
        if (!$this->hasData('related_images')) {
            $connection = $this->getResource()->getConnection();
            $tableName = $this->getResource()->getTable('designnbuy_background_background_images');
            $select = $connection->select()
                ->from(['bgi' => $tableName])
                ->where('bgi.background_id = ?', $this->getId());

            $result = $connection->fetchAll($select);

            $backgrounds = [];
            if ($result) {
                foreach($result as $item) {
                    $image = array();
                    $data = array();
                    if ($file = $this->getData('image')) {
                        $image['name'] = $item['output'];
                        $image['url'] = $this->_url->getBackgroundImagesMediaUrl($item['output']);
                        $image['size'] = $this->helper->getFileSize(\Designnbuy\Background\Model\Background::BASE_MEDIA_PATH . '/' .$file);
                        $data['output'][] = $image;
                    }
                    if ($file = $this->getData('image')) {
                        $image['name'] = $item['display'];
                        $image['url'] = $this->_url->getBackgroundImagesMediaUrl($item['display']);
                        $image['size'] = $this->helper->getFileSize(\Designnbuy\Background\Model\Background::BASE_MEDIA_PATH . '/' .$file);
                        $data['display'][] = $image;
                    }
                    $data['width'] = $item['width'];
                    $data['height'] = $item['height'];
                    $data['image_id'] = $item['image_id'];
                    $backgrounds[] = $data;
                }

                $this->setData('related_images', $backgrounds);
            }
        }

        return $this->getData('related_images');
    }

    public function getRelatedProductBackgrounds($productId, $categoryId)
    {
        $backgroundCollection = $this->backgroundProduct->getRelatedBackgrounds($productId);
        $backgroundCollection
            ->addActiveFilter()
            ->addStoreFilter($this->_storeManager->getStore()->getId(), true)
            ->setOrder('creation_time', 'DESC');
        if(isset($categoryId)){
            $backgroundCollection->addCategoryFilter($categoryId);
        }
        $backgrounds = [];
        foreach ($backgroundCollection as $background) {
            $backgroundImages = $background->getBackgroundImages();
            $images = [];
            if(isset($backgroundImages)){
                foreach($backgroundImages as $backgroundImage){
                    if(isset($backgroundImage['display']) && isset($backgroundImage['output']) ){
                        $images[] = [
                            'width' => $backgroundImage['width'],
                            'height' => $backgroundImage['height'],
                            'output_image' => $backgroundImage['output'][0]['url'],
                            'display_image' => $backgroundImage['display'][0]['url'],
                        ];
                    }
                }
                $backgrounds[] = [
                    'background_id' => $background->getId(),
                    'name' => $background->getStoreLabel($this->_storeManager->getStore()->getId()),
                    'image' => $background->getImage(),
                    'backgrounds' => $images,
                ];
            }
        }
        return $backgrounds;
    }
}
