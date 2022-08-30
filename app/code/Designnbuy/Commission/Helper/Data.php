<?php
namespace Designnbuy\Commission\Helper;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\Result\Redirect;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Designnbuy Redemption Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const STATUS_INACTIVE = 0;

    const ADMIN_USER = 1;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;
 
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
 
    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;
    
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
    * @param Magento\Framework\App\Helper\Context $context
    * @param Magento\Store\Model\StoreManagerInterface $storeManager
    * @param Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
    */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\App\State $state,
        \Magento\Framework\UrlInterface $urlInterface,
        LoggerInterface $logger = null
    ) {
        $this->_scopeConfig = $context;
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->inlineTranslation = $inlineTranslation;
        $this->messageManager = $messageManager;
        $this->_coreRegistry = $coreRegistry;
        $this->authSession = $authSession;
        $this->state = $state;
        $this->_urlInterface = $urlInterface;
        $this->logger = $logger ?: ObjectManager::getInstance()->get(LoggerInterface::class);
    }
 
    /**
     * Return store configuration value of your template field that which id you set for template
     *
     * @param string $path
     * @param int $storeId
     * @return mixed
     */
    protected function getConfigValue($path, $storeId)
    {
        return $this->scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
    
    /**
     * Return store 
     *
     * @return Store
     */
    public function getStore()
    {
        return $this->_storeManager->getStore();
    }
   
    public function getBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }

    public function getCurrentUrl()
    {
        return $this->_urlInterface->getCurrentUrl();
    }

    public function getDesignReportUrl()
    {
        return $this->_storeManager->getStore()->getUrl('designer/commission/designreport');
    }

    public function getCommissionReportUrl()
    {
        return $this->_storeManager->getStore()->getUrl('designer/commission/commissionreport');
    }
    /**
     * Retrieve store config value
     * @param  string $path
     * @return mixed
     */
    public function getConfig($path)
    {
        return $this->scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getOwnerUserId()
    {
        $areaCode = $this->state->getAreaCode();
        if($areaCode != 'frontend'):
            return $this->authSession->getUser()->getUserId();
        endif;
    }

    public function getOwnerUserName()
    {
        return $this->authSession->getUser()->getFirstname(). ' ' . $this->authSession->getUser()->getLastname();
    }

    public function getDefaultStatus()
    {
        return self::STATUS_INACTIVE;
    }

    public function isWebsiteOwner()
    {
        if($this->getOwnerUserId() != self::ADMIN_USER):
            return true;
        else:
            return false;
        endif;
    }

    public function getAreaCode()
    {
        return $this->state->getAreaCode();
    }
}
