<?php
/**
 * Designnbuy_Reseller extension
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category  Designnbuy
 * @package   Designnbuy_Reseller
 * @copyright Copyright (c) 2018
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 */
namespace Designnbuy\Reseller\Controller\Adminhtml\Request;

class Approve extends \Designnbuy\Reseller\Controller\Adminhtml\Request
{
    protected $requestFactory;

    protected $dataObjectProcessor;

    protected $dataObjectHelper;

    protected $dataPersistor;

    protected $_scopeConfig;

    protected $_resellerHelper;

    protected $groupResourceModel;

    protected $storeResourceModel;

    protected $websiteResourceModel;
    
    protected $websiteFactory;
    
    protected $storeFactory;
    
    protected $groupFactory;


    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Designnbuy\Reseller\Api\RequestRepositoryInterface $requestRepository,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Designnbuy\Reseller\Api\Data\RequestInterfaceFactory $requestFactory,
        \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Designnbuy\Reseller\Helper\Data $resellerHelper,
        \Magento\Store\Model\ResourceModel\Group $groupResourceModel,
        \Magento\Store\Model\ResourceModel\Store $storeResourceModel,
        \Magento\Store\Model\ResourceModel\Website $websiteResourceModel,
        \Magento\Store\Model\GroupFactory $groupFactory,
        \Magento\Store\Model\StoreFactory $storeFactory,
        \Magento\Store\Model\WebsiteFactory $websiteFactory
    ) {
        $this->requestFactory      = $requestFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->dataObjectHelper    = $dataObjectHelper;
        $this->dataPersistor       = $dataPersistor;
        $this->_scopeConfig        = $scopeConfig;
        $this->_resellerHelper     = $resellerHelper;
        $this->groupResourceModel  = $groupResourceModel;
        $this->storeResourceModel  = $storeResourceModel;
        $this->websiteResourceModel= $websiteResourceModel;
        $this->websiteFactory = $websiteFactory;
        $this->storeFactory = $storeFactory;
        $this->groupFactory = $groupFactory;
        parent::__construct($context, $coreRegistry, $requestRepository, $resultPageFactory);
    }


    public function execute()
    {
        /** @var \Designnbuy\Reseller\Api\Data\RequestInterface $request */
        $request = null;
        $postData =  $this->getRequest()->getParams();
        $data = $postData;
        if($data['type'] == 'approve'){
            $data['status'] = 1;
        }

        $id = !empty($data['request_id']) ? $data['request_id'] : null;
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            if ($id) {
                $request = $this->requestRepository->getById((int)$id);
            } else {
                $this->messageManager->addErrorMessage(__('Request not found'));
                $resultRedirect->setPath('designnbuy_reseller/request/edit', ['request_id' => $request->getId()]);
                return  $resultRedirect;
            }

            $error = $this->_validate($request);

            if($error){
                $this->messageManager->addErrorMessage(__($error));
                $resultRedirect->setPath('designnbuy_reseller/request/edit', ['request_id' => $request->getId()]);
                return  $resultRedirect;
            }

            $roleId = $this->_scopeConfig->getValue(
                'reseller_settings/reseller_role/role_reseller'
            );

            //$newRoleId = $this->_resellerHelper->createResellerRole($roleId,$request->getEmail());

            $adminUser = $this->_objectManager->create('Magento\User\Model\User');
            $adminData = array('firstname'=>$request->getFirstName(),
                               'lastname' => $request->getLastName(),
                               'email' => $request->getEmail(),
                               'username'=>$request->getUsername(),
                               'password'=>$request->getPassword(),
                               'role_id'=> $roleId
                                );

            $adminUser->setData($adminData);
            $adminUser->save();
            $userId = $adminUser->getId();
            $adminUser->sendNotificationEmailsIfRequired();

            $website = $this->websiteFactory->create();
            $website->load($request->getStoreCode());
            if(!$website->getId()){
                $website->setCode($request->getStoreCode());
                $website->setName($request->getStoreCode());
                $this->websiteResourceModel->save($website);
            }

            //$this->_resellerHelper->updateResellerRole($roleId,$newRoleId,$website->getId());

            $storeManager = $this->_objectManager->create('Magento\Store\Model\StoreManagerInterface');
            $rootCaregoryId = $storeManager->getStore()->getRootCategoryId();

            if($website->getId()){
                /** @var \Magento\Store\Model\Group $group */
                $group = $this->groupFactory->create();
                $group->setWebsiteId($website->getWebsiteId());
                $group->setName($request->getStoreCode());
                $group->setCode($request->getStoreCode());
                $group->setRootCategoryId($rootCaregoryId);
                $this->groupResourceModel->save($group);
            }

            $store = $this->storeFactory->create();
            $store->load($request->getStoreCode());
            if(!$store->getId()){
                $group = $this->groupFactory->create();
                $group->load($request->getStoreCode(), 'name');
                $store->setCode($request->getStoreCode());
                $store->setName($request->getStoreCode());
                $store->setWebsite($website);
                $store->setGroupId($group->getId());
                $store->setData('is_active','1');
                
                $this->storeResourceModel->save($store);
                $this->_objectManager->get('Magento\Store\Model\StoreManager')->reinitStores();
                $this->_eventManager->dispatch('store_add', ['store' => $store]);
                // Trigger event to insert some data to the sales_sequence_meta table (fix bug place order in checkout)
               // $this->eventManager->dispatch('store_add', ['store' => $store]);
            }

            $reseller = $this->_objectManager->create('Designnbuy\Reseller\Model\Resellers');
            $reseller->setWebsiteId($website->getWebsiteId())
                    ->setStoreId($store->getStoreId())
                     ->setUserId($userId)
                     ->setCompanyRegistrationNumber($request->getCompanyRegistrationNumber())
                     ->setStatus(true)
                     ->save();

            $this->dataObjectHelper->populateWithArray($request, $data, \Designnbuy\Reseller\Api\Data\RequestInterface::class);
            $this->requestRepository->save($request);
            $this->messageManager->addSuccessMessage(__('You saved the Request'));
            $this->dataPersistor->clear('designnbuy_reseller_request');
            if ($this->getRequest()->getParam('back')) {
                $resultRedirect->setPath('designnbuy_reseller/request/edit', ['request_id' => $request->getId()]);
            } else {
                $resultRedirect->setPath('designnbuy_reseller/request');
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->dataPersistor->set('designnbuy_reseller_request', $postData);
            $resultRedirect->setPath('designnbuy_reseller/request/edit', ['request_id' => $id]);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__('There was a problem saving the Request'));
            $this->dataPersistor->set('designnbuy_reseller_request', $postData);
            $resultRedirect->setPath('designnbuy_reseller/request/edit', ['request_id' => $id]);
        }
        return $resultRedirect;
    }

    protected function _validate($data){
        $errorMessage = '';
        $storeCode = $data->getStoreCode();

        $website = $this->_objectManager->create('Magento\Store\Model\Website')->getCollection()->addFieldToFilter('name', $storeCode);
        if($website->getSize() > 0){
            $errorMessage = "Website already exits";
        }

        $storeGroup = $this->_objectManager->create('Magento\Store\Model\Group')->getCollection()->addFieldToFilter('name', $storeCode);
        if($storeGroup->getSize() > 0){
            $errorMessage = "Store already exits";
        }

        $store = $this->_objectManager->create('Magento\Store\Model\Store')->getCollection()->addFieldToFilter('name', $storeCode);
        if($store->getSize() > 0){
            $errorMessage = "Store view already exits";
        }
        return $errorMessage;
    }
}
