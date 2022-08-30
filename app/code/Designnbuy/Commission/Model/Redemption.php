<?php

namespace Designnbuy\Commission\Model;

use Designnbuy\Commission\Model\Url;

/**
 * Redemption model
 *
 * @method \Designnbuy\Commission\Model\ResourceModel\Redemption _getResource()
 * @method \Designnbuy\Commission\Model\ResourceModel\Redemption getResource()
 * @method int getStoreId()
 * @method $this setStoreId(int $value)
 * @method string getTitle()
 * @method $this setTitle(string $value)
 * @method string getMetaKeywords()
 */
class Redemption extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Redemption's Statuses
     */
    const STATUS_PAID = 1;
    
    const STATUS_PENDING = 0;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'designnbuy_designer_commission_transaction';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'designnbuy_commission';

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
     * @var \Designnbuy\Commission\Model\Url
     */
    protected $_url;

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
     * @param \Designnbuy\Designer\Model\Url $url
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
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,        
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
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
        $this->_init('Designnbuy\Commission\Model\ResourceModel\Redemption');
        $this->controllerName = URL::CONTROLLER_REDEMPTION;
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
        return $plural ? 'Redemption Request' : 'Redemption';
    }

    /**
     * Deprecated
     * Retrieve true if Redemption is active
     * @return boolean [description]
     */
    public function isActive()
    {
        return ($this->getIsActive() == self::STATUS_PAID);
    }

    /**
     * Retrieve available Redemption statuses
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_PENDING => __('Unpaid'), self::STATUS_PAID => __('Paid')];
    }


    /**
     * Retrieve Redemption url path
     * @return string
     */
    public function getUrl()
    {
        return $this->_url->getUrlPath($this->getIdentifier(), $this->controllerName);
    }

    public function getRedemptionUrl()
    {
        if (!$this->hasData('redemption_url')) {
            $url = $this->_url->getUrl($this, $this->controllerName);
            $this->setData('redemption_url', $url);
        }
        return $this->getData('redemption_url');
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
     * Retrieve Redemption publish date using format
     * @param  string $format
     * @return string
     */
    public function getPublishDate($format = 'Y-m-d H:i:s')
    {
        return \Designnbuy\Commission\Helper\Data::getTranslatedDate(
            $format,
            $this->getData('publish_time')
        );
    }

    /**
     * Retrieve redemption publish date using format
     * @param  string $format
     * @return string
     */
    public function getUpdateDate($format = 'Y-m-d H:i:s')
    {
        return \Designnbuy\Commission\Helper\Data::getTranslatedDate(
            $format,
            $this->getData('update_time')
        );
    }

    /**
     * Retrieve Customer data based to merge in UiDataProvider.
     * @param  string $format
     * @return string
     */
    public function getCustomerInformation()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerModel = $objectManager->create('Magento\Customer\Model\Customer');
        $_customer = $customerModel->load($this->getUserId());
        if($this->getUserId() != ''):
            $this->setData('customer_data', $_customer);
        endif;
        return $this->getData('customer_data');
    }

    /*public function getDesignerInformation() 
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $designerModel = $objectManager->create('Designnbuy\Designer\Model\Designer');
        $_designer = $designerModel->load($this->getUserId(), 'customer_id');
        if($_designer->getEntityId() != ''):
            $this->setData('designer_data', $_designer);
        endif;
        return $this->getData('designer_data');
    }*/

    public function getUserInformation(){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $userModel = $objectManager->create('Magento\User\Model\User');
        $_user = $userModel->load($this->getUserId(), 'user_id');
        if($_user->getUserId() != ''):
            $this->setData('user_data', $_user);
        endif;
        return $this->getData('user_data');
    }

    public function getResellerInformation(){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resellerModel = $objectManager->create('Designnbuy\Reseller\Model\Resellers');
        $_reseller = $resellerModel->load($this->getUserId(), 'user_id');
        if($_reseller->getResellerId() != ''):
            $this->setData('reseller_data', $_reseller);
        endif;
        return $this->getData('reseller_data');
    }
}
