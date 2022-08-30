<?php
/**
 * Copyright © 2019 Design 'N' Buy. All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 * ALWAYS DO BETTER @a
 */

namespace Designnbuy\JobManagement\Helper;

use Magento\Framework\App\Action\Action;
use Magento\Framework\Controller\Result\Redirect;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Store\Model\ScopeInterface;
/**
 * Designnbuy JobManagement Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_EMAIL_TEMPLATE_FIELD  = 'writerbook/writerpricinggroup/email_template';

    const OUTPUT_FILE_PATH = 'designnbuy'. DIRECTORY_SEPARATOR .'output'. DIRECTORY_SEPARATOR;

    const DS = '/';

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
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;
     
    /**
     * @var string
    */
    protected $temp_id;
    
    protected $_mediaDirectory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var \\Designnbuy\JobManagement\Model\JobmanagementFactory
     */
    protected $jobManagementFactory;

    /**
    * @param Magento\Framework\App\Helper\Context $context
    * @param Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    * @param Magento\Store\Model\StoreManagerInterface $storeManager
    * @param Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
    * @param Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
    */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Customer\Model\SessionFactory $customerSession,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Designnbuy\JobManagement\Model\Url $url,
        \Magento\Framework\Filesystem $filesystem,
        LoggerInterface $logger = null,
        \Designnbuy\JobManagement\Model\JobmanagementFactory $jobManagementFactory
    ) {
        $this->_scopeConfig = $context;
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder;
        $this->messageManager = $messageManager;
        $this->_customerSession = $customerSession;
        $this->_scopeConfig = $scopeConfig;
        $this->_url = $url;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->logger = $logger ?: ObjectManager::getInstance()->get(LoggerInterface::class);
        $this->_jobManagementFactory = $jobManagementFactory;
    }

    public function getCustomerSession()
    {
        return $this->_customerSession->create();
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
     * Retrieve translated & formated date
     * @param  string $format
     * @param  string $dateOrTime
     * @return string
     */
    public static function getTranslatedDate($format, $dateOrTime)
    {
        $time = is_numeric($dateOrTime) ? $dateOrTime : strtotime($dateOrTime);
        $month = ['F' => '%1', 'M' => '%2'];

        foreach ($month as $from => $to) {
            $format = str_replace($from, $to, $format);
        }

        $date = date($format, $time);

        foreach ($month as $to => $from) {
            $date = str_replace($from, __(date($to, $time)), $date);
        }

        return $date;
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


    /**
     * [sendInvoicedOrderEmail description]                  
     * @param  Mixed $emailTemplateVariables 
     * @param  Mixed $senderInfo             
     * @param  Mixed $receiverInfo           
     * @return void
     */
    /* your send mail method*/
    public function sendWriterNotificationMailToAdmin($originalRequestData)
    {
        $senderInfo = [
            'name' => $this->getConfig(self::XML_PATH_EMAIL_SENDER_NAME),
            'email' => $this->getConfig(self::XML_PATH_EMAIL_SENDER_EMAIL),
        ];
        $receiverInfo = [
            'name' => $originalRequestData['firstname']. ' ' .$originalRequestData['lastname'],
            'email' => $originalRequestData['email']
        ];
                     
        $emailTemplateVariables = array();
        $emailTemplateVariables['password'] = $originalRequestData['password'];

        $this->temp_id = $this->getTemplateId(self::XML_PATH_EMAIL_TEMPLATE_FIELD);
        $this->inlineTranslation->suspend();    
        $this->generateTemplate($emailTemplateVariables,$senderInfo,$receiverInfo);    
        $transport = $this->_transportBuilder->getTransport();

        try {
            $transport->sendMessage();        
            $this->inlineTranslation->resume(); 
        } catch (\Exception $e) {
            $this->logger->critical($e);
            $this->messageManager->addErrorMessage(
                __('An email not send.')
            );
        }
        /*$transport->sendMessage();        
        $this->inlineTranslation->resume();*/
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

    public function _getConfigValue($param)
    {
        return $this->_scopeConfig->getValue($param, ScopeInterface::SCOPE_STORE);
    }


    public function getIsEnabled()
    {
        return $this->_getConfigValue('writerbook/writerbookgroup/enabled');
    }

    public function checkWriterStatus()
    {
        $_customerId = $this->getCustomerSession()->getId();
        $writer = $this->getWriterByCustomerId($_customerId);
        $_writer = $this->getCustomerInformation($_customerId);
        if($_writer->getCustomertype() == self::WRITER_TYPE && $writer->getIsActive() == 1){
            return 1;
        }
        return 0;
    }

    public function getOutputDownloadUrl($orderIncrementId, $itemId)    
    {
        $file = $this->_mediaDirectory->getAbsolutePath(self::OUTPUT_FILE_PATH) . 'Order_'.$orderIncrementId . '_'.$itemId.'_Designs'.'.zip';
        $fileUrl = 'Order_'.$orderIncrementId . '_'.$itemId.'_Designs'.'.zip';
        $filePath = $this->_url->getMediaUrl(self::OUTPUT_FILE_PATH). $fileUrl;
        
        if(!file_exists($file)){
            return false;
        }else {
            return $filePath;
        }
    }

    public function getEmptyPath(){
        return false;
    }
    /**
     * Return template id according to store
     *
     * @return mixed
     */
    public function getTemplateId($xmlPath)
    {
        return $this->getConfigValue($xmlPath, $this->getStore()->getStoreId());
    }

    /**
     * [generateTemplate description]  with template file and tempaltes variables values                
     * @param  Mixed $emailTemplateVariables 
     * @param  Mixed $senderInfo             
     * @param  Mixed $receiverInfo           
     * @return void
     */
    public function generateTemplate($emailTemplateVariables,$senderInfo,$receiverInfo)
    {
        $template =  $this->_transportBuilder->setTemplateIdentifier($this->temp_id)
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_ADMINHTML, 
                        'store' => $this->_storeManager->getStore()->getId(),
                    ]
                )
                ->setTemplateVars($emailTemplateVariables)
                ->setFrom($senderInfo)
                ->addTo($receiverInfo['email'],$receiverInfo['name']);
        return $this;        
    }

    public function getJobContent($entityId) {
        $job = $this->_jobManagementFactory->create()->load($entityId);
        return $job;
    }
}
