<?php
namespace Designnbuy\Notifications\Model;

/**
 * Notifications model
 *
 * @method \Designnbuy\Notifications\Model\ResourceModel\Notifications _getResource()
 * @method \Designnbuy\Notifications\Model\ResourceModel\Notifications getResource()
 * @method int getStoreId()
 * @method $this setStoreId(int $value)
 * @method string getTitle()
 * @method $this setTitle(string $value)
 * @method string getMetaKeywords()
 */
class Notifications extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Notifications's Statuses
     */
    const STATUS_READ = 1;

    const STATUS_UNREAD = 0;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'designnbuy_marketplacenotification_inbox';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'notifications_notifications';

    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $filterProvider;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Designnbuy\Designer\Model\Url
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
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Designnbuy\Notifications\Helper\Data $notificationHelper,        
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,        
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->notificationHelper = $notificationHelper;
        $this->filterProvider = $filterProvider;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Designnbuy\Notifications\Model\ResourceModel\Notifications');
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
        return $plural ? 'Notifications' : 'Notification';
    }

    /**
     * Deprecated
     * Retrieve true if Notifications is active
     * @return boolean [description]
     */
    public function isActive()
    {
        return ($this->getIsActive() == self::STATUS_ENABLED);
    }

    /**
     * Retrieve available Notifications statuses
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_DISABLED => __('Disabled'), self::STATUS_ENABLED => __('Enabled')];
    }

    #Commission RedeemRequest
    public function commissionRedemNotificationMessage($requestRequest)
    {
        $notification['title'] = 'Commission redeem request';
        $notification['type'] = $this->notificationHelper::TYPE_REDEEM;
        $notification['type_id'] = $requestRequest['id'];
        $notification['description'] = 'Commission redeem request from '.$requestRequest['name'];
        $notification['creation_time'] = $this->notificationHelper->getCurrentDate();
        $notification['update_time'] = $this->notificationHelper->getCurrentDate();
        $this->addData($notification);
        $this->save();
        return;
    }

    #Designer Request
    public function designerRequestNotificationMessage($requestRequest){      
        $notification['title'] = 'New designer request';
        $notification['type'] = $this->notificationHelper::TYPE_DESIGNER;
        $notification['type_id'] = $requestRequest['id'];
        $notification['description'] = 'Designer request from '.$requestRequest['name'];
        
        $notification['creation_time'] = $this->notificationHelper->getCurrentDate();
        $notification['update_time'] = $this->notificationHelper->getCurrentDate();
        $this->addData($notification);
        $this->save();
        return;
    }

    #Reseller Request
    public function resellerRequestNotificationMessage($resellerRequest){
        $notification['title'] = 'New reseller request';
        $notification['type'] = $this->notificationHelper::TYPE_RESELLER;
        $notification['type_id'] = $resellerRequest['id'];
        $notification['description'] = 'Reseller request from '.$resellerRequest['name'];
        
        $notification['creation_time'] = $this->notificationHelper->getCurrentDate();
        $notification['update_time'] = $this->notificationHelper->getCurrentDate();
        $this->addData($notification);
        $this->save();
        return;
    }

    #Design publish notification Request
    public function publishDesignNotification($requestRequest) {     
        $notification['title'] = 'Request for publish design';
        $notification['type'] = $this->notificationHelper::TYPE_DESIGN;
        $notification['type_id'] = $requestRequest['id'];
        $notification['product_type'] = $requestRequest['product_type'];
        $notification['description'] = 'Publish design request from '.$requestRequest['designer_name'];

        $notification['creation_time'] = $this->notificationHelper->getCurrentDate();
        $notification['update_time'] = $this->notificationHelper->getCurrentDate();
        $this->addData($notification);
        $this->save();
        return;
    }
    
    #Design un-publish notification Request
    public function unpublishDesignNotification($requestRequest){       
        $notification['title'] = 'Design Unpublished';
        $notification['type'] = $this->notificationHelper::TYPE_UNPUBLISED;
        $notification['type_id'] = $requestRequest['id'];
        $notification['product_type'] = $requestRequest['product_type'];
        $notification['description'] = 'Design unpublished by '.$requestRequest['designer_name'];

        $notification['creation_time'] = $this->notificationHelper->getCurrentDate();
        $notification['update_time'] = $this->notificationHelper->getCurrentDate();
        $this->addData($notification);
        $this->save();
        return;
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
     * Retrieve Notifications publish date using format
     * @param  string $format
     * @return string
     */
    /*public function getUpdateDate($format = 'Y-m-d H:i:s')
    {
        return \Designnbuy\Designer\Helper\Data::getTranslatedDate(
            $format,
            $this->getData('update_time')
        );
    }*/
}
