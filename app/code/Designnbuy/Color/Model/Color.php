<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Color\Model;

use Designnbuy\Color\Model\Url;

/**
 * Color model
 *
 * @method \Designnbuy\Color\Model\ResourceModel\Color _getResource()
 * @method \Designnbuy\Color\Model\ResourceModel\Color getResource()
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
class Color extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Colors's Statuses
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
    const BASE_MEDIA_PATH = 'designnbuy'. DIRECTORY_SEPARATOR .'color';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'designnbuy_color_color';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'color_color';

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $filterProvider;

    /**
     * @var \Designnbuy\Color\Model\Url
     */
    protected $_url;

    /**
     * @var \Designnbuy\Color\Model\AuthorFactory
     */
    protected $_authorFactory;

    /**
     * @var \Designnbuy\Color\Model\ResourceModel\Category\CollectionFactory
     */
    protected $_categoryCollectionFactory;

    /**
     * @var \Designnbuy\Color\Model\ResourceModel\Tag\CollectionFactory
     */
    protected $_tagCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var \Designnbuy\Color\Model\ResourceModel\Category\Collection
     */
    protected $_parentCategories;

    /**
     * @var \Designnbuy\Color\Model\ResourceModel\Tag\Collection
     */
    protected $_relatedTags;

    /**
     * @var \Designnbuy\Color\Model\ResourceModel\Color\Collection
     */
    protected $_relatedColorsCollection;

    /**
     * @var \Designnbuy\Color\Model\ImageFactory
     */
    protected $imageFactory;

    /**
     * @var \Designnbuy\Color\Model\ResourceModel\Color\Product
     */
    protected $colorProduct;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Designnbuy\Color\Model\Url $url
     * @param \Designnbuy\Color\Model\AuthorFactory $authorFactory
     * @param \Designnbuy\Color\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \Designnbuy\Color\Model\ResourceModel\Tag\CollectionFactory $tagCollectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        Url $url,
        \Designnbuy\Color\Model\ImageFactory $imageFactory,
        \Designnbuy\Color\Model\AuthorFactory $authorFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Designnbuy\Color\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Designnbuy\Color\Model\ResourceModel\Tag\CollectionFactory $tagCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Designnbuy\Color\Model\ResourceModel\Color\Product $colorProduct,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,

        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->filterProvider = $filterProvider;
        $this->_url = $url;
        $this->_storeManager = $storeManager;
        $this->imageFactory = $imageFactory;
        $this->_authorFactory = $authorFactory;
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
        $this->_tagCollectionFactory = $tagCollectionFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->colorProduct = $colorProduct;
        $this->_relatedColorsCollection = clone($this->getCollection());
        $this->productRepository = $productRepository;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Designnbuy\Color\Model\ResourceModel\Color');
    }

    /**
     * Retrieve model title
     * @param  boolean $plural
     * @return string
     */
    public function getOwnTitle($plural = false)
    {
        return $plural ? 'Colors' : 'Color';
    }

    /**
     * Retrieve true if color is active
     * @return boolean [description]
     */
    public function isActive()
    {
        return ($this->getStatus() == self::STATUS_ENABLED);
    }

    /**
     * Retrieve available color statuses
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_DISABLED => __('Disabled'), self::STATUS_ENABLED => __('Enabled')];
    }

    /**
     * Check if color identifier exist for specific store
     * return color id if color exists
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
     * Retrieve color url route path
     * @return string
     */
    public function getUrl()
    {
        return $this->_url->getUrlPath($this, URL::CONTROLLER_COLOR);
    }

    /**
     * Retrieve color url
     * @return string
     */
    public function getColorUrl()
    {
        if (!$this->hasData('color_url')) {
            $url = $this->_url->getUrl($this, URL::CONTROLLER_COLOR);
            $this->setData('color_url', $url);
        }

        return $this->getData('color_url');
    }


    /**
     * Retrieve featured image url
     * @return string
     */
    public function getImage()
    {
        if (!$this->hasData('color_image')) {
            if ($file = $this->getData('image')) {
                $image = $this->_url->getMediaUrl($file);
            } else {
                $image = false;
            }
            $this->setData('color_image', $image);
        }

        return $this->getData('color_image');
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
     * Retrieve color parent categories
     * @return \Designnbuy\Color\Model\ResourceModel\Category\Collection
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
     * Retrieve color parent categories count
     * @return int
     */
    public function getCategoriesCount()
    {
        return count($this->getParentCategories());
    }

    /**
     * Retrieve color tags
     * @return \Designnbuy\Color\Model\ResourceModel\Tag\Collection
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
     * Retrieve color tags count
     * @return int
     */
    public function getTagsCount()
    {
        return count($this->getRelatedTags());
    }

    /**
     * Retrieve color related colors
     * @return \Designnbuy\Color\Model\ResourceModel\Color\Collection
     */
    public function getRelatedColors()
    {
        if (!$this->hasData('related_colors')) {
            $collection = $this->_relatedColorsCollection
                ->addFieldToFilter('color_id', ['neq' => $this->getId()])
                ->addStoreFilter($this->getStoreId());
            $collection->getSelect()->joinLeft(
                ['rl' => $this->getResource()->getTable('designnbuy_color_color_relatedcolor')],
                'main_table.color_id = rl.related_id',
                ['position']
            )->where(
                'rl.color_id = ?',
                $this->getId()
            );
            $this->setData('related_colors', $collection);
        }

        return $this->getData('related_colors');
    }

    /**
     * Retrieve color related products
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
                ['rl' => $this->getResource()->getTable('designnbuy_color_color_relatedproduct')],
                'e.entity_id = rl.related_id',
                ['position']
            )->where(
                'rl.color_id = ?',
                $this->getId()
            );

            $this->setData('related_products', $collection);
        }

        return $this->getData('related_products');
    }

    /**
     * Retrieve color author
     * @return \Designnbuy\Color\Model\Author | false
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
     * Retrieve color publish date using format
     * @param  string $format
     * @return string
     */
    public function getPublishDate($format = 'Y-m-d H:i:s')
    {
        return \Designnbuy\Color\Helper\Data::getTranslatedDate(
            $format,
            $this->getData('publish_time')
        );
    }

    /**
     * Retrieve color publish date using format
     * @param  string $format
     * @return string
     */
    public function getUpdateDate($format = 'Y-m-d H:i:s')
    {
        return \Designnbuy\Color\Helper\Data::getTranslatedDate(
            $format,
            $this->getData('update_time')
        );
    }

    /**
     * Temporary method to get images from some custom color version. Do not use this method.
     * @param  string $format
     * @return string
     */
    public function getColorImage()
    {
        $image = $this->getData('image');
        if (!$image) {
            $image = $this->getData('color_image');
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
            'color_url',
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
     * Duplicate color and return new object
     * @return self
     */
    public function duplicate()
    {
        $object = clone $this;
        $object
            ->unsetData('color_id')
            ->setTitle($object->getTitle() . ' (' . __('Duplicated') . ')')
            ->setData('is_active', 0);

        $relatedProductIds = $this->getRelatedProducts()->getAllIds();
        $relatedPcolorIds = $this->getRelatedColors()->getAllIds();

        $object->setData(
            'links',
            [
                'product' => array_combine($relatedProductIds, $relatedProductIds),
                'color' => array_combine($relatedPcolorIds, $relatedPcolorIds),
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
     * Get store id
     *
     * @return int Store id
     */
    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getStoreId();
    }
    
    public function getRelatedProductColors($product)
    {

        $storeId = 0;
        if($this->getStoreId()){
            $storeId = $this->getStoreId();
        }
        if(is_object($product)){
            $_product = $product;
        } else {
            $_product = $this->productRepository->getById($product);
        }

        /*For Product Specific Category Specific Colors*/
        $categories[] = $_product->getColorCategory();
        $relatedColors = $this->getResourceCollection();
        $relatedColors
            ->addActiveFilter()
            ->addCategoryFilter($categories)
            //->addStoreFilter($this->_storeManager->getStore()->getId())
            ->setOrder('creation_time', 'DESC');

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $state = $objectManager->get('\Magento\Framework\App\State');

        if($state->getAreaCode() == \Magento\Framework\App\Area::AREA_ADMINHTML){
            $storeId =  \Magento\Store\Model\Store::DEFAULT_STORE_ID;
        }else{
            $storeId =  $this->_storeManager->getStore()->getId();
        }
        $relatedColors->addStoreFilter($storeId, true);
        /*For Product Specific Colors*/
        /*$productId = $product;
        $relatedColors = $this->colorProduct->getRelatedColors($productId);
        $relatedColors
            ->addActiveFilter()
            ->addStoreFilter($this->_storeManager->getStore()->getId())
            ->setOrder('creation_time', 'DESC');*/

        $colors = [];
        
        foreach ($relatedColors as $relatedColor) {
            if($_product->getAllowSpotColorOutput()){
                if($relatedColor->getCmykColorCode() != '' ){
                    $cmykColor = explode(' ',trim($relatedColor->getCmykColorCode()));
                    if(count($cmykColor) == 4){
                        $name = $relatedColor->getStoreLabel($storeId) != ''
                            ? $relatedColor->getStoreLabel($storeId)
                            : $relatedColor->getTitle();
                        $color = preg_replace('/[\s]*/', '', $name); // remove extra spaces
                        $color = strtolower($color);

                        $colors[] = [
                            'name' => $color,
                            'colorCode' => $relatedColor->getColorCode(),
                            'cmykColorCode' => $cmykColor
                        ];
                    }
                }
            } else {
                $colors[] = [
                    'name' => $relatedColor->getStoreLabel($storeId) != ''
                        ? $relatedColor->getStoreLabel($storeId)
                        : $relatedColor->getTitle(),
                    'colorCode' => $relatedColor->getColorCode(),
                    'cmykColorCode' => $relatedColor->getCmykColorCode()
                ];
            }

        }

        return $colors;
    }
    
    public function getRelatedBackgroundProductColors($product)
    {

        $storeId = 0;
        if($this->getStoreId()){
            $storeId = $this->getStoreId();
        }
        if(is_object($product)){
            $_product = $product;
        } else {
            $_product = $this->productRepository->getById($product);
        }

        /*For Product Specific Category Specific Colors*/
        $categories[] = $_product->getBackgroundColorCategory();
        $relatedColors = $this->getResourceCollection();
        $relatedColors
            ->addActiveFilter()
            ->addCategoryFilter($categories)
            //->addStoreFilter($this->_storeManager->getStore()->getId())
            ->setOrder('creation_time', 'DESC');

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $state = $objectManager->get('\Magento\Framework\App\State');

        if($state->getAreaCode() == \Magento\Framework\App\Area::AREA_ADMINHTML){
            $storeId =  \Magento\Store\Model\Store::DEFAULT_STORE_ID;
        }else{
            $storeId =  $this->_storeManager->getStore()->getId();
        }
        $relatedColors->addStoreFilter($storeId, true);
        /*For Product Specific Colors*/
        /*$productId = $product;
        $relatedColors = $this->colorProduct->getRelatedColors($productId);
        $relatedColors
            ->addActiveFilter()
            ->addStoreFilter($this->_storeManager->getStore()->getId())
            ->setOrder('creation_time', 'DESC');*/

        $colors = [];
        
        foreach ($relatedColors as $relatedColor) {
            if($_product->getAllowSpotColorOutput()){
                if($relatedColor->getCmykColorCode() != '' ){
                    $cmykColor = explode(' ',trim($relatedColor->getCmykColorCode()));
                    if(count($cmykColor) == 4){
                        $name = $relatedColor->getStoreLabel($storeId) != ''
                            ? $relatedColor->getStoreLabel($storeId)
                            : $relatedColor->getTitle();
                        $color = preg_replace('/[\s]*/', '', $name); // remove extra spaces
                        $color = strtolower($color);

                        $colors[] = [
                            'name' => $color,
                            'colorCode' => $relatedColor->getColorCode(),
                            'cmykColorCode' => $cmykColor
                        ];
                    }
                }
            } else {
                $colors[] = [
                    'name' => $relatedColor->getStoreLabel($storeId) != ''
                        ? $relatedColor->getStoreLabel($storeId)
                        : $relatedColor->getTitle(),
                    'colorCode' => $relatedColor->getColorCode(),
                    'cmykColorCode' => $relatedColor->getCmykColorCode()
                ];
            }

        }

        return $colors;
    }

    public function getProductPrintableColors($product)
    {
        $storeId = 0;
        if($this->getStoreId()){
            $storeId = $this->getStoreId();
        }
        if(is_object($product)){
            $_product = $product;
        } else {
            $_product = $this->productRepository->getById($product);
        }

        /*For Product Specific Category Specific Colors*/
        $categories[] = $_product->getColorCategory();
        $relatedColors = $this->getResourceCollection();
        $relatedColors
            ->addActiveFilter()
            ->addCategoryFilter($categories)
            //->addStoreFilter($this->_storeManager->getStore()->getId())
            ->setOrder('creation_time', 'DESC');

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $state = $objectManager->get('\Magento\Framework\App\State');

        if($state->getAreaCode() == \Magento\Framework\App\Area::AREA_ADMINHTML){
            $storeId =  \Magento\Store\Model\Store::DEFAULT_STORE_ID;
        }else{
            $storeId =  $this->_storeManager->getStore()->getId();
        }
        $relatedColors->addStoreFilter($storeId, true);
        /*For Product Specific Colors*/
        /*$productId = $product;
        $relatedColors = $this->colorProduct->getRelatedColors($productId);
        $relatedColors
            ->addActiveFilter()
            ->addStoreFilter($this->_storeManager->getStore()->getId())
            ->setOrder('creation_time', 'DESC');*/

        $colors = [];

        foreach ($relatedColors as $relatedColor) {
            if($relatedColor->getCmykColorCode() != '' ){
                $cmykColor = explode(' ',trim($relatedColor->getCmykColorCode()));
                if(count($cmykColor) == 4){
                    $name = $relatedColor->getStoreLabel($storeId) != ''
                        ? $relatedColor->getStoreLabel($storeId)
                        : $relatedColor->getTitle();
                    // $color = preg_replace('/[\s]*/', '', $name); // remove extra spaces
                    // $color = strtolower($color);
                    $color = $name;
                    $colors[$relatedColor->getColorCode()] = [
                        'name' => $color,
                        'colorCode' => $relatedColor->getColorCode(),
                        'cmykColorCode' => $cmykColor
                    ];
                }
            }
        }

        return $colors;
    }

}
