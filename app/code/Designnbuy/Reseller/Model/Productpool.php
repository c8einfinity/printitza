<?php
namespace Designnbuy\Reseller\Model;

use Designnbuy\Reseller\Model\Url;

/**
 * Reseller Productpool model
 *
 * @method \Designnbuy\Reseller\Model\ResourceModel\Productpool _getResource()
 * @method \Designnbuy\Reseller\Model\ResourceModel\Productpool getResource()
 * @method int getStoreId()
 * @method $this setStoreId(int $value)
 * @method string getTitle()
 * @method $this setTitle(string $value)
 * @method string getMetaKeywords()
 */
class Productpool extends \Magento\Framework\Model\AbstractModel 
{
    /**
     * Productpools's Statuses
     */
    const STATUS_ENABLED = 1;

    const STATUS_DISABLED = 0;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'designnbuy_reseller_productpool';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'reseller_productpool';

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
     * @var \Designnbuy\Reseller\Model\Url
     */
    protected $_url;

    /**
     * @var string
     */
    protected $controllerName;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Math\Random $random
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Designnbuy\Reseller\Model\Url $url
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
        Url $url,        
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->filterProvider = $filterProvider;
        $this->random = $random;
        $this->scopeConfig = $scopeConfig;
        $this->_url = $url;        
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Designnbuy\Reseller\Model\ResourceModel\Productpool');
        $this->controllerName = URL::CONTROLLER_PRODUCTPOOL;
    }


    /**
     * Retrieve controller name
     * @return string
     */
    public function getControllerName()
    {
        return $this->controllerName;
    }

    /**
     * Retrieve model title
     * @param  boolean $plural
     * @return string
     */
    public function getOwnTitle($plural = false)
    {
        return $plural ? 'Product pools' : 'Product pool';
    }

    /**
     * Deprecated
     * Retrieve true if Productpool is active
     * @return boolean [description]
     */
    public function isActive()
    {
        return ($this->getIsActive() == self::STATUS_ENABLED);
    }

    /**
     * Retrieve available Productpool statuses
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_DISABLED => __('Disabled'), self::STATUS_ENABLED => __('Enabled')];
    }


    /**
     * Retrieve Productpool url path
     * @return string
     */
    /*public function getUrl()
    {
        return $this->_url->getUrlPath($this->getIdentifier(), $this->controllerName);
    }*/

    /**
     * Retrieve Productpool url
     * @return string
     */
    public function getProductpollUrl()
    {
        if (!$this->hasData('productpool_url')) {
            $url = $this->_url->getUrl($this, $this->controllerName);
            $this->setData('productpool_url', $url);
        }

        return $this->getData('productpool_url');
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

    public function getUpdateDate($format = 'Y-m-d H:i:s')
    {
        return \Designnbuy\Reseller\Helper\Data::getTranslatedDate(
            $format,
            $this->getData('update_time')
        );
    }

    /**
     * Retrieve post related products
     * @return \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    public function getProductpoolProducts()
    {
        if (!$this->hasData('productpool_products')) {
            $collection = $this->_productCollectionFactory->create();

            if ($this->getStoreId()) {
                $collection->addStoreFilter($this->getStoreId());
            }

            $collection->getSelect()->joinLeft(
                ['rl' => $this->getResource()->getTable('designnbuy_reseller_productpool_products')],
                'e.entity_id = rl.product_id',
                ['position']
            )->where(
                'rl.productpool_id = ?',
                $this->getId()
            );

            $this->setData('productpool_products', $collection);
        }

        return $this->getData('productpool_products');
    }

}
