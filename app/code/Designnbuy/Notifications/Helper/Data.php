<?php

namespace Designnbuy\Notifications\Helper;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\Result\Redirect;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\ObjectManager;

/**
 * Designnbuy Notifications Helper
 */

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const TYPE_DESIGNER = 1;
        const TEXT_DESIGNER = 'Designer Request';

    const TYPE_RESELLER = 2;
        const TEXT_RESELLER = 'Shop Request';

    const TYPE_DESIGN = 3;
        const TEXT_DESIGN = 'Publish Design';

    const TYPE_REPORT = 4;
        const TEXT_REPORT = 'Abuse Report';

    const TYPE_REDEEM = 5;
        const TEXT_REDEEM = 'Commission Redeem';

    const TYPE_UNPUBLISED = 6;
        const TEXT_UNPUBLISED = 'Design Unpublished';

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
    * @param Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    * @param Magento\Store\Model\StoreManagerInterface $storeManager
    * @param Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
    */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        LoggerInterface $logger = null
    ) {
        $this->_scopeConfig = $context;
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->inlineTranslation = $inlineTranslation;
        $this->messageManager = $messageManager;
        $this->_date = $date;
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
    public function getCurrentDate()
    {
        return $this->_date->gmtDate();    
    }
    
}
