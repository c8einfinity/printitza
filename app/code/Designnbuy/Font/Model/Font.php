<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Font\Model;

use Designnbuy\Font\Model\Url;

/**
 * Font model
 *
 * @method \Designnbuy\Font\Model\ResourceModel\Font _getResource()
 * @method \Designnbuy\Font\Model\ResourceModel\Font getResource()
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
class Font extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Fonts's Statuses
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
    const BASE_MEDIA_PATH = 'designnbuy'. DIRECTORY_SEPARATOR .'font';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'designnbuy_font_font';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'font_font';

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $filterProvider;

    /**
     * @var \Designnbuy\Font\Model\Url
     */
    protected $_url;

    /**
     * @var \Designnbuy\Font\Model\ResourceModel\Category\CollectionFactory
     */
    protected $_categoryCollectionFactory;    

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var \Designnbuy\Font\Model\ResourceModel\Category\Collection
     */
    protected $_parentCategories;

    /**
     * @var \Designnbuy\Font\Model\ResourceModel\Font\Collection
     */
    protected $_relatedFontsCollection;

    /**
     * @var \Designnbuy\Font\Model\ImageFactory
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
     * @param \Designnbuy\Font\Model\Url $url
     * @param \Designnbuy\Font\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
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
        \Designnbuy\Font\Model\ImageFactory $imageFactory,
        \Designnbuy\Font\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->filterProvider = $filterProvider;
        $this->_fileSystem = $filesystem;
        $this->_url = $url;
        $this->_storeManager = $storeManager;
        $this->imageFactory = $imageFactory;
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_relatedFontsCollection = clone($this->getCollection());
    }
    public  function getFontBasePath()
    {
        return $this->_fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath().self::BASE_MEDIA_PATH;
    }
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Designnbuy\Font\Model\ResourceModel\Font');
    }

    /**
     * Retrieve model title
     * @param  boolean $plural
     * @return string
     */
    public function getOwnTitle($plural = false)
    {
        return $plural ? 'Fonts' : 'Font';
    }

    /**
     * Retrieve true if font is active
     * @return boolean [description]
     */
    public function isActive()
    {
        return ($this->getStatus() == self::STATUS_ENABLED);
    }

    /**
     * Retrieve available font statuses
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_DISABLED => __('Disabled'), self::STATUS_ENABLED => __('Enabled')];
    }

    /**
     * Check if font identifier exist for specific store
     * return font id if font exists
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
     * Retrieve font url route path
     * @return string
     */
    public function getUrl()
    {
        return $this->_url->getUrlPath($this, URL::CONTROLLER_CLIPART);
    }

    /**
     * Retrieve font url
     * @return string
     */
    public function getFontUrl()
    {
        if (!$this->hasData('font_url')) {
            $url = $this->_url->getUrl($this, URL::CONTROLLER_CLIPART);
            $this->setData('font_url', $url);
        }

        return $this->getData('font_url');
    }


    /**
     * Retrieve featured image url
     * @return string
     */
    public function getWoff()
    {
        if (!$this->hasData('font_woff')) {
            if ($file = $this->getData('woff')) {
                $image = $this->_url->getMediaUrl($file);
            } else {
                $image = false;
            }
            $this->setData('font_woff', $image);
        }

        return $this->getData('font_woff');
    }
    public function getTtf()
    {
        if (!$this->hasData('font_ttf')) {
            if ($file = $this->getData('ttf')) {
                $image = $this->_url->getMediaUrl($file);
            } else {
                $image = false;
            }
            $this->setData('font_ttf', $image);
        }

        return $this->getData('font_ttf');
    }
    public function getTtfBold()
    {
        if (!$this->hasData('font_ttfbold')) {
            if ($file = $this->getData('ttfbold')) {
                $image = $this->_url->getMediaUrl($file);
            } else {
                $image = false;
            }
            $this->setData('font_ttfbold', $image);
        }

        return $this->getData('font_ttfbold');
    }
    public function getTtfItalic()
    {
        if (!$this->hasData('font_ttfitalic')) {
            if ($file = $this->getData('ttfitalic')) {
                $image = $this->_url->getMediaUrl($file);
            } else {
                $image = false;
            }
            $this->setData('font_ttfitalic', $image);
        }

        return $this->getData('font_ttfitalic');
    }
    public function getTtfBoldItalic()
    {
        if (!$this->hasData('font_ttfbolditalic')) {
            if ($file = $this->getData('ttfbolditalic')) {
                $image = $this->_url->getMediaUrl($file);
            } else {
                $image = false;
            }
            $this->setData('font_ttfbolditalic', $image);
        }

        return $this->getData('font_ttfbolditalic');
    }
    
    /**
     * Retrieve featured image url
     * @return string
     */
    public function getJs()
    {
        if (!$this->hasData('font_js')) {
            if ($file = $this->getData('js')) {
                $image = $this->_url->getMediaUrl($file);
            } else {
                $image = false;
            }
            $this->setData('font_js', $image);
        }

        return $this->getData('font_js');
    }
    /**
     * Retrieve featured image url
     * @return string
     */
    public function getCss()
    {
        if (!$this->hasData('font_css')) {
            if ($file = $this->getData('css')) {
                $image = $this->_url->getMediaUrl($file);
            } else {
                $image = false;
            }
            $this->setData('font_css', $image);
        }

        return $this->getData('font_css');
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
     * Retrieve font parent categories
     * @return \Designnbuy\Font\Model\ResourceModel\Category\Collection
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
     * Retrieve font parent categories count
     * @return int
     */
    public function getCategoriesCount()
    {
        return count($this->getParentCategories());
    }

    /**
     * Retrieve font related fonts
     * @return \Designnbuy\Font\Model\ResourceModel\Font\Collection
     */
    public function getRelatedFonts()
    {
        if (!$this->hasData('related_fonts')) {
            $collection = $this->_relatedFontsCollection
                ->addFieldToFilter('font_id', ['neq' => $this->getId()])
                ->addStoreFilter($this->getStoreId());
            $collection->getSelect()->joinLeft(
                ['rl' => $this->getResource()->getTable('designnbuy_font_font_relatedfont')],
                'main_table.font_id = rl.related_id',
                ['position']
            )->where(
                'rl.font_id = ?',
                $this->getId()
            );
            $this->setData('related_fonts', $collection);
        }

        return $this->getData('related_fonts');
    }

    /**
     * Retrieve font related products
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
                ['rl' => $this->getResource()->getTable('designnbuy_font_font_relatedproduct')],
                'e.entity_id = rl.related_id',
                ['position']
            )->where(
                'rl.font_id = ?',
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
     * Temporary method to get images from some custom font version. Do not use this method.
     * @param  string $format
     * @return string
     */
    public function getFontImage()
    {
        $image = $this->getData('image');
        if (!$image) {
            $image = $this->getData('font_image');
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
            'woff',
            'js',
            'font_url',
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
     * Duplicate font and return new object
     * @return self
     */
    public function duplicate()
    {
        $object = clone $this;
        $object
            ->unsetData('font_id')
            ->setTitle($object->getTitle() . ' (' . __('Duplicated') . ')')
            ->setData('is_active', 0);

        $relatedProductIds = $this->getRelatedProducts()->getAllIds();
        $relatedPfontIds = $this->getRelatedFonts()->getAllIds();

        $object->setData(
            'links',
            [
                'product' => array_combine($relatedProductIds, $relatedProductIds),
                'font' => array_combine($relatedPfontIds, $relatedPfontIds),
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
     * Retrieve font related products
     * @return \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    public function getProductRelatedFonts($productId = null)
    {
        if (!$this->hasData('product_related_fonts')) {
            $collection = $this->_relatedFontsCollection
                ->addActiveFilter()
                ->setOrder('title','ASC');

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $state = $objectManager->get('\Magento\Framework\App\State');

            if($state->getAreaCode() == \Magento\Framework\App\Area::AREA_ADMINHTML){
                $storeId =  \Magento\Store\Model\Store::DEFAULT_STORE_ID;
            }else{
                $storeId =  $this->_storeManager->getStore()->getId();
            }
            $collection->addStoreFilter($storeId, true);
            
            if($productId){
                $fontCollection = clone $collection;
                $fontCollection->getSelect()->joinLeft(
                    ['rl' => $this->getResource()->getTable('designnbuy_font_font_relatedproduct')],
                    'main_table.font_id = rl.font_id',
                    ['position']
                )->where(
                    'rl.related_id = ?',
                    $productId
                );
                if($fontCollection->getSize() == 0){
                    $fontCollection = clone $collection;
                }
            } else {
                $fontCollection = clone $collection;
            }
            $fonts = [];
            foreach ($fontCollection as $font) {
                if($font->getCss() && $font->getJs()){
                    $fonts[$font->getTitle()] = [
                            'id' => $font->getId(),
                            'cssFile' => $font->getCss(),
                            'jsFile' => $font->getJs(),
                            'isTtf' => empty($font->getTtf())?false:true,
                            'isTtfBold' => empty($font->getTtfbold())?false:true,
                            'isTtfItalic' => empty($font->getTtfitalic())?false:true,
                            'isTtfBoldItalic' => empty($font->getTtfbolditalic())?false:true,
                    ];
                }
            }
            $this->setData('product_related_fonts', $fonts);
        }

        return $this->getData('product_related_fonts');
    }

}
