<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\PrintingMethod\Model;

use Designnbuy\PrintingMethod\Model\Url;

/**
 * PrintingMethod model
 *
 * @method \Designnbuy\PrintingMethod\Model\ResourceModel\PrintingMethod _getResource()
 * @method \Designnbuy\PrintingMethod\Model\ResourceModel\PrintingMethod getResource()
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
class PrintingMethod extends \Magento\Framework\Model\AbstractModel
{
    /**
     * PrintingMethods's Statuses
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
    const BASE_MEDIA_PATH = 'designnbuy'. DIRECTORY_SEPARATOR .'printingmethod';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'designnbuy_printingmethod_printingmethod';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'printingmethod_printingmethod';

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $filterProvider;

    /**
     * @var \Designnbuy\PrintingMethod\Model\Url
     */
    protected $_url;


    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var \Designnbuy\PrintingMethod\Model\ResourceModel\Category\Collection
     */
    protected $_parentCategories;

    

    /**
     * @var \Designnbuy\PrintingMethod\Model\ResourceModel\PrintingMethod\Collection
     */
    protected $_relatedPrintingMethodsCollection;

    /**
     * @var \Designnbuy\PrintingMethod\Model\ImageFactory
     */
    protected $imageFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Designnbuy\PrintingMethod\Model\Url $url
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
        \Designnbuy\PrintingMethod\Model\ImageFactory $imageFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->filterProvider = $filterProvider;
        $this->_url = $url;
        $this->imageFactory = $imageFactory;

        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_storeManager = $storeManager;
        $this->_relatedPrintingMethodsCollection = clone($this->getCollection());
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Designnbuy\PrintingMethod\Model\ResourceModel\PrintingMethod');
    }

    /**
     * Retrieve model title
     * @param  boolean $plural
     * @return string
     */
    public function getOwnTitle($plural = false)
    {
        return $plural ? 'Printing Methods' : 'Printing Method';
    }

    /**
     * Retrieve true if printingmethod is active
     * @return boolean [description]
     */
    public function isActive()
    {
        return ($this->getStatus() == self::STATUS_ENABLED);
    }

    /**
     * Retrieve available printingmethod statuses
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_DISABLED => __('Disabled'), self::STATUS_ENABLED => __('Enabled')];
    }

    /**
     * Check if printingmethod identifier exist for specific store
     * return printingmethod id if printingmethod exists
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
     * Retrieve printingmethod url route path
     * @return string
     */
    public function getUrl()
    {
        return $this->_url->getUrlPath($this, URL::CONTROLLER_PRINTINGMETHOD);
    }

    /**
     * Retrieve printingmethod url
     * @return string
     */
    public function getPrintingMethodUrl()
    {
        if (!$this->hasData('printingmethod_url')) {
            $url = $this->_url->getUrl($this, URL::CONTROLLER_PRINTINGMETHOD);
            $this->setData('printingmethod_url', $url);
        }

        return $this->getData('printingmethod_url');
    }


    /**
     * Retrieve featured image url
     * @return string
     */
    public function getImage()
    {
        if (!$this->hasData('printingmethod_image')) {
            if ($file = $this->getData('image')) {
                $image = $this->_url->getMediaUrl($file);
            } else {
                $image = false;
            }
            $this->setData('printingmethod_image', $image);
        }

        return $this->getData('printingmethod_image');
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
     * Retrieve printingmethod related products
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
                ['rl' => $this->getResource()->getTable('designnbuy_printingmethod_printingmethod_relatedproduct')],
                'e.entity_id = rl.related_id',
                ['position']
            )->where(
                'rl.printingmethod_id = ?',
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
     * Retrieve printingmethod publish date using format
     * @param  string $format
     * @return string
     */
    public function getPublishDate($format = 'Y-m-d H:i:s')
    {
        return \Designnbuy\PrintingMethod\Helper\Data::getTranslatedDate(
            $format,
            $this->getData('publish_time')
        );
    }

    /**
     * Retrieve printingmethod publish date using format
     * @param  string $format
     * @return string
     */
    public function getUpdateDate($format = 'Y-m-d H:i:s')
    {
        return \Designnbuy\PrintingMethod\Helper\Data::getTranslatedDate(
            $format,
            $this->getData('update_time')
        );
    }

    /**
     * Temporary method to get images from some custom printingmethod version. Do not use this method.
     * @param  string $format
     * @return string
     */
    public function getPrintingMethodImage()
    {
        $image = $this->getData('image');
        if (!$image) {
            $image = $this->getData('printingmethod_image');
        }
        return $image;
    }


    /**
     * Duplicate printingmethod and return new object
     * @return self
     */
    public function duplicate()
    {
        $object = clone $this;
        $object
            ->unsetData('printingmethod_id')
            ->setTitle($object->getTitle() . ' (' . __('Duplicated') . ')')
            ->setData('is_active', 0);

        $relatedProductIds = $this->getRelatedProducts()->getAllIds();
        //$relatedPprintingmethodIds = $this->getRelatedPrintingMethods()->getAllIds();

        $object->setData(
            'links',
            [
                'product' => array_combine($relatedProductIds, $relatedProductIds),
                //'printingmethod' => array_combine($relatedPprintingmethodIds, $relatedPprintingmethodIds),
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
     * Retrieve printingmethod related printable colors
     * @return \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    public function getRelatedColors()
    {
        if (!$this->hasColors()) {
            $colors = $this->_getResource()->getColors($this->getId());
            $this->setColors($colors);
        }

        return $this->_getData('colors');
    }

    /**
     * Retrieve printingmethod related quantity area price
     * @return \Designnbuy\PrintingMethod\Model\ResourceModel\PrintingMethod
     */
    public function getRelatedQAPrices()
    {
        if (!$this->hasQaPrices()) {
            $qaPrices = $this->_getResource()->getQAPrices($this->getId());
            $this->setQaPrices($qaPrices);
        }

        return $this->_getData('qa_prices');
    }

    /**
     * Retrieve printingmethod related quantity colors price
     * @return \Designnbuy\PrintingMethod\Model\ResourceModel\PrintingMethod
     */
    public function getRelatedQCPrices()
    {
        if (!$this->hasQcPrices()) {
            $qcPrices = $this->_getResource()->getQCPrices($this->getId());
            $this->setQcPrices($qcPrices);
        }

        return $this->_getData('qc_prices');
    }
    /**
     * Retrieve printingmethod related quantity price
     * @return \Designnbuy\PrintingMethod\Model\ResourceModel\PrintingMethod
     */
    public function getRelatedQPrices()
    {
        if (!$this->hasQPrices()) {
            $qPrices = $this->_getResource()->getQPrices($this->getId());
            $this->setQPrices($qPrices);
        }

        return $this->_getData('q_prices');
    }

    public function getPrintingMethodQPrice($qty, $side)
    {
        $qPrice = 0;
        $printingMethodQPrice = [];
        $printingMethodQPrice = $this->_getResource()->getPrintingMethodQPrice($this->getId(), $qty, $side);
        if(isset($printingMethodQPrice) && isset($printingMethodQPrice['price'])){
            $qPrice = $printingMethodQPrice['price'];
        }
        return $qPrice;
    }

    public function getPrintingMethodQCPrice($totalColors, $qty, $side)
    {
        $qcPrice = 0;
        $printingMethodQCPrice = [];
        $printingMethodQCPrice = $this->_getResource()->getPrintingMethodQCPrice($this->getId(), $totalColors, $qty, $side);
        if(isset($printingMethodQCPrice) && isset($printingMethodQCPrice['price'])){
            $qcPrice = $printingMethodQCPrice['price'];
        }
        return $qcPrice;
    }

    public function getPrintingMethodQAPrice($area, $qty, $side)
    {
        $qaPrice = 0;
        $printingMethodQAPrice = [];
        $printingMethodQAPrice = $this->_getResource()->getPrintingMethodQAPrice($this->getId(), $area, $qty, $side);
        if(isset($printingMethodQAPrice) && isset($printingMethodQAPrice['price'])){
            $qaPrice = $printingMethodQAPrice['price'];
        }
        return $qaPrice;
    }


}
