<?php
/**
 * Copyright © Designnbuy (support@designnbuy.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Designnbuy\Sheet\Model;


/**
 * Size model
 *
 * @method \Designnbuy\Sheet\Model\ResourceModel\Size _getResource()
 * @method \Designnbuy\Sheet\Model\ResourceModel\Size getResource()
 * @method int getStoreId()
 * @method $this setStoreId(int $value)
 * @method string getTitle()
 * @method $this setTitle(string $value)
 */
class Size extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    /**
     * Sizes's Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    /**
     * sheet cache size
     */
    const CACHE_TAG = 'mfb_p';

    /**
     * Gallery images separator
     */
    const GALLERY_IMAGES_SEPARATOR = ';';

    /**
     * Base media folder path
     */
    const BASE_MEDIA_PATH = 'designnbuy_sheet';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'designnbuy_sheet_size';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'sheet_size';

    /**
     * @var \Magento\Framework\Math\Random
     */
    protected $random;

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $filterProvider;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Designnbuy\Sheet\Model\AuthorFactory
     */
    protected $_authorFactory;


    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;


    /**
     * @var \Designnbuy\Sheet\Model\ResourceModel\Size\Collection
     */
    protected $_relatedSizesCollection;


    /**
     * @var string
     */
    protected $controllerName;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Math\Random $random
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Designnbuy\Sheet\Model\AuthorFactory $authorFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Math\Random $random,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->filterProvider = $filterProvider;
        $this->random = $random;
        $this->scopeConfig = $scopeConfig;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_relatedSizesCollection = clone($this->getCollection());
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Designnbuy\Sheet\Model\ResourceModel\Size');
    }

    /**
     * Retrieve identities
     *
     * @return array
     */
    public function getIdentities()
    {
        $identities = [];

        if ($this->getId()) {
            $identities[] = self::CACHE_TAG . '_' . $this->getId();
        }

        $oldCategories = $this->getOrigData('categories');
        if (!is_array($oldCategories)) {
            $oldCategories = [];
        }

        $newCategories = $this->getData('categories');
        if (!is_array($newCategories)) {
            $newCategories = [];
        }


        $isChangedCategories = count(array_diff($oldCategories, $newCategories));

        if ($isChangedCategories) {
            $changedCategories = array_unique(
                array_merge($oldCategories, $newCategories)
            );
            foreach ($changedCategories as $categoryId) {
                $identities[] = \Designnbuy\Sheet\Model\Category::CACHE_TAG . '_' . $categoryId;
            }
        }


        return $identities;
    }

    /**
     * Retrieve block identifier
     *
     * @return string
     */
    public function getIdentifier()
    {
        return (string)$this->getData('identifier');
    }
    /**
     * Retrieve model title
     * @param  boolean $plural
     * @return string
     */
    public function getOwnTitle($plural = false)
    {
        return $plural ? 'Sizes' : 'Size';
    }

    /**
     * Deprecated
     * Retrieve true if size is active
     * @return boolean [description]
     */
    public function isActive()
    {
        return ($this->getIsActive() == self::STATUS_ENABLED);
    }

    /**
     * Retrieve available size statuses
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_DISABLED => __('Disabled'), self::STATUS_ENABLED => __('Enabled')];
    }

    /**
     * Retrieve featured image url
     * @return string
     */
    public function getFeaturedImage()
    {
        if (!$this->hasData('featured_image')) {
            if ($file = $this->getData('featured_img')) {
                $image = $this->_url->getMediaUrl($file);
            } else {
                $image = false;
            }
            $this->setData('featured_image', $image);
        }

        return $this->getData('featured_image');
    }

    /**
     * Set media gallery images url
     *
     * @param array $images
     * @return this
     */
    public function setGalleryImages(array $images)
    {
        $this->setData(
            'media_gallery',
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
            $images = [];
            $gallery = $this->getData('media_gallery');
            if ($gallery && !is_array($gallery)) {
                $gallery = explode(
                    self::GALLERY_IMAGES_SEPARATOR,
                    $gallery
                );
            }
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
            $image = $this->getFeaturedImage();
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
     * @param  mixed $len
     * @param  mixed $endСharacters
     * @return string
     */
    public function getShortFilteredContent($len = null, $endСharacters = null)
    {
        $key = 'short_filtered_content' . $len;
        if (!$this->hasData($key)) {
            if ($this->getShortContent()) {
                $content = $this->filterProvider->getPageFilter()->filter(
                    $this->getShortContent()
                );
            } else {
                $content = $this->getFilteredContent();

                if (!$len) {
                    $pageBraker = '<!-- pagebreak -->';
                    $len = mb_strpos($content, $pageBraker);
                    if (!$len) {
                        $len = (int)$this->scopeConfig->getValue(
                            'mfsheet/size_list/shortcotent_length',
                            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                        );
                    }
                }
            }

            if ($len) {
                /* Do not cut words */
                while ($len < strlen($content)
                    && !in_array($content{$len}, [' ', '<', "\t", "\r", "\n"]) ) {
                    $len++;
                }

                $content = mb_substr($content, 0, $len);
                try {
                    $previousLoaderState = libxml_disable_entity_loader(true);
                    $previousErrorState = libxml_use_internal_errors(true);
                    $dom = new \DOMDocument();
                    $dom->loadHTML('<?xml encoding="UTF-8">' . $content);
                    libxml_disable_entity_loader($previousLoaderState);
                    libxml_use_internal_errors($previousErrorState);

                    $body = $dom->getElementsByTagName('body');
                    if ($body && $body->length > 0) {
                        $body = $body->item(0);
                        $_content = new \DOMDocument;
                        foreach ($body->childNodes as $child) {
                            $_content->appendChild($_content->importNode($child, true));
                        }
                        $content = $_content->saveHTML();
                    }
                } catch (\Exception $e) {
                }
            }

            if ($len && $endСharacters) {
                $trimMask = " \t\n\r\0\x0B,.!?";
                if ($p = strrpos($content, '</')) {
                    $content = trim(substr($content, 0, $p), $trimMask)
                        . $endСharacters
                        . substr($content, $p);
                } else {
                    $content = trim($content, $trimMask)
                        . $endСharacters;
                }
            }
            
            $this->setData($key, $content);
        }

        return $this->getData($key);
        ;
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
        if (mb_strlen($desc) > 300) {
            $desc = mb_substr($desc, 0, 300);
        }

        return trim($desc);
    }

    /**
     * Retrieve og title
     * @return string
     */
    public function getOgTitle()
    {
        $title = $this->getData('og_title');
        if (!$title) {
            $title = $this->getMetaTitle();
        }

        return trim($title);
    }

    /**
     * Retrieve og description
     * @return string
     */
    public function getOgDescription()
    {
        $desc = $this->getData('og_description');
        if (!$desc) {
            $desc = $this->getMetaDescription();
        } else {
            $desc = strip_tags($desc);
            if (mb_strlen($desc) > 160) {
                $desc = mb_substr($desc, 0, 160);
            }
        }

        return trim($desc);
    }

    /**
     * Retrieve og type
     * @return string
     */
    public function getOgType()
    {
        $type = $this->getData('og_type');
        if (!$type) {
            $type = 'article';
        }

        return trim($type);
    }

    /**
     * Retrieve og image url
     * @return string
     */
    public function getOgImage()
    {
        if (!$this->hasData('og_image')) {
            if ($file = $this->getData('og_img')) {
                $image = $this->_url->getMediaUrl($file);
            } else {
                $image = false;
            }
            $this->setData('og_image', $image);
        }

        return $this->getData('og_image');
    }

    /**
     * Retrieve size related sizes
     * @return \Designnbuy\Sheet\Model\ResourceModel\Size\Collection
     */
    public function getRelatedSizes()
    {
        if (!$this->hasData('related_sizes')) {
            $collection = $this->_relatedSizesCollection
                ->addFieldToFilter('size_id', ['neq' => $this->getId()])
                ->addStoreFilter($this->getStoreId());
            $collection->getSelect()->joinLeft(
                ['rl' => $this->getResource()->getTable('designnbuy_sheet_size_relatedsize')],
                'main_table.size_id = rl.related_id',
                ['position']
            )->where(
                'rl.size_id = ?',
                $this->getId()
            );
            $this->setData('related_sizes', $collection);
        }
        return $this->getData('related_sizes');
    }

    /**
     * Retrieve size related products
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
                ['rl' => $this->getResource()->getTable('designnbuy_sheet_size_relatedproduct')],
                'e.entity_id = rl.related_id',
                ['position']
            )->where(
                'rl.size_id = ?',
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
        return $this->getIsActive()
            && $this->getData('publish_time') <= $this->getResource()->getDate()->gmtDate()
            && array_intersect([0, $storeId], $this->getStoreIds());
    }

    /**
     * Retrieve if is preview secret is valid
     * @return bool
     */
    public function isValidSecret($secret)
    {
        return ($secret && $this->getSecret() === $secret);
    }

    /**
     * Retrieve size publish date using format
     * @param  string $format
     * @return string
     */
    public function getPublishDate($format = 'Y-m-d H:i:s')
    {
        return \Designnbuy\Sheet\Helper\Data::getTranslatedDate(
            $format,
            $this->getData('publish_time')
        );
    }

    /**
     * Retrieve size publish date using format
     * @param  string $format
     * @return string
     */
    public function getUpdateDate($format = 'Y-m-d H:i:s')
    {
        return \Designnbuy\Sheet\Helper\Data::getTranslatedDate(
            $format,
            $this->getData('update_time')
        );
    }

    /**
     * Temporary method to get images from some custom sheet version. Do not use this method.
     * @param  string $format
     * @return string
     */
    public function getSizeImage()
    {
        $image = $this->getData('featured_img');
        if (!$image) {
            $image = $this->getData('size_image');
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
            'og_image',
            'og_type',
            'og_description',
            'og_title',
            'meta_description',
            'meta_title',
            'short_filtered_content',
            'filtered_content',
            'first_image',
            'featured_image',
            'size_url',
        ];

        foreach ($keys as $key) {
            $method = 'get' . str_replace(
                '_',
                '',
                ucwords($key, '_')
            );
            $this->$method();
        }

        return $this;
    }

    /**
     * Duplicate size and return new object
     * @return self
     */
    public function duplicate()
    {
        $object = clone $this;
        $object
            ->unsetData('size_id')
            ->unsetData('creation_time')
            ->unsetData('update_time')
            ->unsetData('publish_time')
            ->unsetData('identifier')
            ->setTitle($object->getTitle() . ' (' . __('Duplicated') . ')')
            ->setData('is_active', 0);

        $relatedProductIds = $this->getRelatedProducts()->getAllIds();
        $relatedPsizeIds = $this->getRelatedSizes()->getAllIds();

        $object->setData(
            'links',
            [
                'product' => array_combine($relatedProductIds, $relatedProductIds),
                'size' => array_combine($relatedPsizeIds, $relatedPsizeIds),
            ]
        );

        return $object->save();
    }

    /**
     * Retrieve secret key of size, it can be used during preview
     * @return string
     */
    public function getSecret()
    {
        if ($this->getId() && !$this->getData('secret')) {
            $this->setData(
                'secret',
                $this->random->getRandomString(32)
            );
            $this->save();
        }

        return $this->getData('secret');
    }

    /**
     * Retrieve updated at time
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->getData('update_time');
    }
}
