<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\ConfigArea\Model;

use Designnbuy\ConfigArea\Model\Url;

/**
 * ConfigArea model
 *
 * @method \Designnbuy\ConfigArea\Model\ResourceModel\ConfigArea _getResource()
 * @method \Designnbuy\ConfigArea\Model\ResourceModel\ConfigArea getResource()
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
class ConfigArea extends \Magento\Framework\Model\AbstractModel
{
    /**
     * ConfigAreas's Statuses
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
    const BASE_MEDIA_PATH = 'designnbuy'. DIRECTORY_SEPARATOR .'configarea';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'designnbuy_configarea_configarea';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'configarea_configarea';

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $filterProvider;

    /**
     * @var \Designnbuy\ConfigArea\Model\Url
     */
    protected $_url;


    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var \Designnbuy\ConfigArea\Model\ResourceModel\Category\Collection
     */
    protected $_parentCategories;

    

    /**
     * @var \Designnbuy\ConfigArea\Model\ResourceModel\ConfigArea\Collection
     */
    protected $_relatedConfigAreasCollection;

    /**
     * @var \Designnbuy\ConfigArea\Model\ImageFactory
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
     * @param \Designnbuy\ConfigArea\Model\Url $url
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
        \Designnbuy\ConfigArea\Model\ImageFactory $imageFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->filterProvider = $filterProvider;
        $this->_url = $url;
        $this->imageFactory = $imageFactory;
        $this->_storeManager = $storeManager;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_relatedConfigAreasCollection = clone($this->getCollection());
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Designnbuy\ConfigArea\Model\ResourceModel\ConfigArea');
    }

    /**
     * Retrieve model title
     * @param  boolean $plural
     * @return string
     */
    public function getOwnTitle($plural = false)
    {
        return $plural ? 'Design Areas' : 'Design Area';
    }

    /**
     * Retrieve true if configarea is active
     * @return boolean [description]
     */
    public function isActive()
    {
        return ($this->getStatus() == self::STATUS_ENABLED);
    }

    /**
     * Retrieve available configarea statuses
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_DISABLED => __('Disabled'), self::STATUS_ENABLED => __('Enabled')];
    }


    /**
     * Retrieve configarea url route path
     * @return string
     */
    public function getUrl()
    {
        return $this->_url->getUrlPath($this, URL::CONTROLLER_CONFIGAREA);
    }

    /**
     * Retrieve configarea url
     * @return string
     */
    public function getConfigAreaUrl()
    {
        if (!$this->hasData('configarea_url')) {
            $url = $this->_url->getUrl($this, URL::CONTROLLER_CONFIGAREA);
            $this->setData('configarea_url', $url);
        }

        return $this->getData('configarea_url');
    }


    /**
     * Retrieve featured image url
     * @return string
     */
    public function getImage()
    {
        if (!$this->hasData('configarea_image')) {
            if ($file = $this->getData('image')) {
                $image = $this->_url->getMediaUrl($file);
            } else {
                $image = false;
            }
            $this->setData('configarea_image', $image);
        }

        return $this->getData('configarea_image');
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
     * Retrieve configarea related products
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
                ['rl' => $this->getResource()->getTable('designnbuy_configarea_configarea_relatedproduct')],
                'e.entity_id = rl.related_id',
                ['position']
            )->where(
                'rl.configarea_id = ?',
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
     * Retrieve configarea publish date using format
     * @param  string $format
     * @return string
     */
    public function getPublishDate($format = 'Y-m-d H:i:s')
    {
        return \Designnbuy\ConfigArea\Helper\Data::getTranslatedDate(
            $format,
            $this->getData('publish_time')
        );
    }

    /**
     * Retrieve configarea publish date using format
     * @param  string $format
     * @return string
     */
    public function getUpdateDate($format = 'Y-m-d H:i:s')
    {
        return \Designnbuy\ConfigArea\Helper\Data::getTranslatedDate(
            $format,
            $this->getData('update_time')
        );
    }

    /**
     * Temporary method to get images from some custom configarea version. Do not use this method.
     * @param  string $format
     * @return string
     */
    public function getConfigAreaImage()
    {
        $image = $this->getData('image');
        if (!$image) {
            $image = $this->getData('configarea_image');
        }
        return $image;
    }


    /**
     * Duplicate configarea and return new object
     * @return self
     */
    public function duplicate()
    {
        $object = clone $this;
        $object
            ->unsetData('configarea_id')
            ->setTitle($object->getTitle() . ' (' . __('Duplicated') . ')')
            ->setData('is_active', 0);

        //$relatedProductIds = $this->getRelatedProducts()->getAllIds();
        //$relatedPconfigareaIds = $this->getRelatedConfigAreas()->getAllIds();

        /*$object->setData(
            'links',
            [
                'product' => array_combine($relatedProductIds, $relatedProductIds),
                'configarea' => array_combine($relatedPconfigareaIds, $relatedPconfigareaIds),
            ]
        );*/

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

}