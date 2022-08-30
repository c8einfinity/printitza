<?php

namespace Designnbuy\Reseller\Controller\Account;

class Create extends \Magento\Framework\App\Action\Action
{
    protected $_resellerSession;
    protected $_storeManager;
    protected $_website;
    protected $_resource;
    protected $requestRepository;
    protected $requestFactory;
    protected $dataObjectHelper;
    protected $_transportBuilder;
    protected $_scopeConfig;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Session\Generic $resellerSession,
        \Magento\Store\Model\ResourceModel\Website $website,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Designnbuy\Reseller\Api\RequestRepositoryInterface $requestRepository,
        \Designnbuy\Reseller\Api\Data\RequestInterfaceFactory $requestFactory,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Designnbuy\Notifications\Model\Notifications $notificationFactory
    ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->_resellerSession = $resellerSession;
        $this->_website = $website;
        $this->_resource = $resourceConnection;
        $this->requestRepository = $requestRepository;
        $this->requestFactory      = $requestFactory;
        $this->dataObjectHelper    = $dataObjectHelper;
        $this->_transportBuilder = $transportBuilder;
        $this->_scopeConfig = $scopeConfig;
        $this->notificationFactory = $notificationFactory;
    }

    protected function _redirectReferer()
    {
        $this->_redirect($this->_redirect->getRedirectUrl());
    }

    public function execute()
    {
        $post = $this->getRequest()->getPostValue();

        if (!$post) {
            $this->_redirectReferer();
            return;
        }

        $allWebsite = $this->_website->readAllWebsites();

        if(array_key_exists($post['store_code'],$allWebsite)){
            $this->messageManager->addError("Store with code already exists");
            $this->_redirectReferer();
            return;
        }

        $connection = $this->_resource->getConnection();

        $select = $connection->select()->from($connection->getTableName('admin_user'))->where('email=:email');
        $binds = ['email' => $post['email']];
        $resultEmail = $connection->fetchRow($select, $binds);
        
        if($resultEmail){
            $this->messageManager->addError("A Reseller with the same email already exists.");
            $this->_redirectReferer();
            return;
        }

        $post['store_code'] = strtolower($post['store_code']);

        $selectUser = $connection->select()->from($connection->getTableName('admin_user'))->where('username=:username');
        $bindsUser = ['username' => $post['username']];
        $resultUser = $connection->fetchRow($selectUser, $bindsUser);
        
        if($resultUser){
            $this->messageManager->addError("A Reseller with the same Username already exists.");
            $this->_redirectReferer();
            return;
        }

        try {

            $request = $this->requestFactory->create();
            $this->dataObjectHelper->populateWithArray($request, $post, \Designnbuy\Reseller\Api\Data\RequestInterface::class);
            $this->requestRepository->save($request);

            //Reseller Request Notification sent
            $notification = array();
            $notification['name'] = $request->getFirstname() . ' ' . $request->getLastname();
            $notification['id'] = $request->getRequestId();
            $_notification = $this->notificationFactory;
            $_notification->resellerRequestNotificationMessage($notification);
            //End

            $store = $this->_storeManager->getStore();

            $from = 'general';
            $to = [
                'email' =>  $post['email'],
                'name' => $post['first_name']." ".$post['last_name']
            ];

            $templateId = $this->_scopeConfig->getValue(
                'reseller_settings/reseller_email/email_template',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $store
            );

            $vars = $post;
            $vars['status'] = 'Pending';

            $this->_sendEmail($from, $to, $templateId, $vars, $store);

            $this->messageManager->addSuccess(__('Your request has been sent. Please wait for the approval.'));
            $this->_redirectReferer();
            return;
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
            $this->_resellerSession->setFormData(
                $post
            )->setRedirectUrl(
                $this->_redirect->getRefererUrl()
            );
            $this->_redirectReferer();
            return;
        }
    }
    
    protected function _sendEmail($from, $to, $templateId, $vars, $store, $area = \Magento\Framework\App\Area::AREA_FRONTEND)
    {
        $this->_transportBuilder
            ->setTemplateIdentifier($templateId)
            ->setTemplateOptions([
                'area' => $area,
                'store' => $store->getId()
            ])
            ->setTemplateVars($vars)
            ->setFrom($from)
            ->addTo($to['email'], $to['name']);
        $transport = $this->_transportBuilder->getTransport();
        $transport->sendMessage();
    }
}
