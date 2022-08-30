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
namespace Designnbuy\Reseller\Controller\Adminhtml\Resellers;

use Magento\Framework\Indexer\IndexerRegistry;
use Magento\Backend\App\Action;
use Magento\Theme\Model\Design\Config\FileUploader\FileProcessor;
use Magento\Theme\Model\Data\Design\ConfigFactory;
use Magento\Framework\App\Config\ReinitableConfigInterface;
use Magento\Theme\Model\Design\Config\Storage as ConfigStorage;
use Magento\Theme\Model\Data\Design\Config as DesignConfig;

class Save extends \Magento\Backend\App\Action
{
    const PERCENTAGE = 1;

    const FIXED = 2;

    protected $_resellerHelper;

    protected $_scopeConfig;

    protected $fileProcessor;

    protected $configStorage;

    protected $reinitableConfig;

    protected $indexerRegistry;

    protected $groupResourceModel;

    protected $storeResourceModel;

    protected $websiteResourceModel;

    protected $websiteFactory;

    protected $storeFactory;

    protected $groupFactory;

    /**
     *  @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    protected $configWriter;

    public function __construct(
        Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Designnbuy\Reseller\Helper\Data $resellerHelper,
        FileProcessor $fileProcessor,
        ConfigFactory $configFactory,
        ReinitableConfigInterface $reinitableConfig,
        IndexerRegistry $indexerRegistry,
        ConfigStorage $configStorage,
        \Magento\Store\Model\ResourceModel\Group $groupResourceModel,
        \Magento\Store\Model\ResourceModel\Store $storeResourceModel,
        \Magento\Store\Model\ResourceModel\Website $websiteResourceModel,
        \Magento\Store\Model\GroupFactory $groupFactory,
        \Magento\Store\Model\StoreFactory $storeFactory,
        \Magento\Store\Model\WebsiteFactory $websiteFactory,        
        \Designnbuy\Reseller\Model\ResourceModel\Products\Collection $productsFactory,
        \Magento\Framework\Indexer\IndexerInterfaceFactory $indexerFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection
    )
    {
        $this->_scopeConfig = $scopeConfig;
        $this->configWriter = $configWriter;
        $this->_resellerHelper = $resellerHelper;
        $this->fileProcessor = $fileProcessor;
        $this->configFactory = $configFactory;
        $this->reinitableConfig = $reinitableConfig;
        $this->indexerRegistry = $indexerRegistry;
        $this->configStorage = $configStorage;
        $this->groupResourceModel  = $groupResourceModel;
        $this->storeResourceModel  = $storeResourceModel;
        $this->websiteResourceModel= $websiteResourceModel;
        $this->websiteFactory = $websiteFactory;
        $this->storeFactory = $storeFactory;
        $this->groupFactory = $groupFactory;
        $this->productsFactory = $productsFactory;
        $this->indexerFactory = $indexerFactory;
        $this->productCollection = $productCollection;
        parent::__construct($context);
    }

    public function execute()
    {
        
        $data = $this->getRequest()->getPostValue();

        $productPools = $this->getRequest()->getParam('product_ids', -1);
        $productPoolIds = [];
        if ($productPools != -1) 
        {
            $ids = $this->_objectManager->get(\Magento\Backend\Helper\Js::class)->decodeGridSerializedInput($productPools);
            $productPoolIds = array_keys($ids);
        }
        
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            if ($data) {
                $model = $this->_objectManager->create('Designnbuy\Reseller\Model\Resellers');
                $adminUser = $this->_objectManager->create('Magento\User\Model\User');
                $website = $this->_objectManager->create('Magento\Store\Model\Website');  
                $storeView = $this->_objectManager->create('Magento\Store\Model\Store');

                $id = $this->getRequest()->getParam('reseller_id');

                if ($id) 
                {
                    $importParams = $this->getRequest()->getParam('import_id');
                    $model->load($id);
                    
                    //Assign Product pool products to website
                    if ($importParams != '') {
                        if(isset($productPoolIds) && !empty($productPoolIds)):
                            $productPoolIdsImplode = implode(",", $productPoolIds);
                            $model->setProductpool($productPoolIdsImplode);
                            $model->save();

                            //Add products to website
                            $commissionType = $model->getCommissionType();
                            $productCommission = $model->getProductCommission();
                            $storeId = $model->getStoreId();
                            if(isset($productCommission) && $productCommission != ''):
                                $this->addProductsToWebsites($model->getWebsiteId(), $productPoolIds, $commissionType, $productCommission, $storeId);
                            else:
                                $this->messageManager->addError(__('Set Product Commission.'));
                                return $resultRedirect->setPath('*/*/edit', ['reseller_id' => $model->getId()], array('active_tab' => 'reseller_info'));
                            endif;
                        endif;
                    }else {

                        //$model->setCommission($data['commission']);
                        $model->setBankDetail($data['bank_detail']);
                        $model->setVatNumber($data['vat_number']);
                        $model->setCompanyRegistrationNumber($data['company_registration_number']);
                        
                        if(isset($data['product_commission'])):
                            $model->setProductCommission($data['product_commission']);
                        endif;
                        
                        if(isset($data['commission_type'])):
                            $model->setCommissionType($data['commission_type']);
                        endif;

                        $model->save();
                        
                        $adminUser->load($model->getUserId());
                        $adminUser->addData($this->_getAdminUserData($data));
                        $website->load($model->getWebsiteId());                        
                        $adminUser->save();
                        $website->save();

                        $store = $this->storeFactory->create();
                        $store->load($model->getStoreId());

                        if(isset($data['is_active'])):
                            $store->setIsActive($data['is_active']);
                        endif;
                        $store->save();

                        if(isset($data['header_logo_src']['delete']) && $data['header_logo_src']['delete']){
                            $logoData['header_logo_src'][0] = '';
                            $scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
                            $designConfigData = $this->configFactory->create($scope, $data['store_id'], $logoData);
                            $this->configStorage->delete($designConfigData);
                            
                        }
                        $adminUser->sendNotificationEmailsIfRequired();
                        $eventName = 'store_add';
                        $this->_objectManager->get(\Magento\Store\Model\StoreManager::class)->reinitStores();
                        $this->_eventManager->dispatch($eventName, ['store' => $store]);
                    }                   

                } else 
                {
                    $error = $this->_validate($data);
                    if($error) {
                        $this->messageManager->addErrorMessage(__($error));
                        $resultRedirect->setPath('*/*/edit');
                        return  $resultRedirect;
                    }

                    $roleId = $this->_scopeConfig->getValue(
                        'reseller_settings/reseller_role/role_reseller'
                    );

                    //$newRoleId = $this->_resellerHelper->createResellerRole($roleId,$data['username']);
                    $adminData = array(
                        'firstname' => $data['firstname'],
                        'lastname' => $data['lastname'],
                        'email' => $data['email'],
                        'username' => $data['username'],
                        'password' => $data['password'],
                        'is_active' => $data['is_active'],
                        'role_id' => $roleId
                    );

                    $adminUser->setData($adminData);
                    $adminUser->save();
                    $userId = $adminUser->getId();

                    $websiteModel = $this->websiteFactory->create();
                    $websiteData = array();
                    $websiteData['name'] = strtolower($data['store_code']);
                    $websiteData['code'] = strtolower($data['store_code']);
                    $websiteModel->setData($websiteData);
                    $websiteModel->save();
                    
                    //#addStoreGroup
                    $storeGroup = $this->groupFactory->create();
                    $storeGroup->setWebsiteId($websiteModel->getWebsiteId())
                            ->setCode(strtolower($data['store_code']))
                            ->setName(strtolower($data['store_code']))
                            ->setRootCategoryId(2)
                            ->save();

                    //#addStore
                    $store = $this->storeFactory->create();
                    $store->setCode(strtolower($data['store_code']))
                        ->setWebsiteId($websiteModel->getWebsiteId())
                        ->setGroupId($storeGroup->getGroupId())
                        ->setName(strtolower($data['store_code']))
                        ->setIsActive($data['is_active'])
                        ->save();

                    $model->setWebsiteId($websiteModel->getWebsiteId())
                            ->setStoreId($store->getStoreId())
                            ->setUserId($userId)
                            ->setCompanyRegistrationNumber($data['company_registration_number'])
                            ->setStatus(true)
                            ->save();

                    //if ($data['store_id'] == '') {
                        $eventName = 'store_add';
                        $this->_objectManager->get(\Magento\Store\Model\StoreManager::class)->reinitStores();
                        $this->_eventManager->dispatch($eventName, ['store' => $store]);
                    //}
                }

                $adminUser->sendNotificationEmailsIfRequired();
                if(isset($_FILES['header_logo_src']['size']) && $_FILES['header_logo_src']['size'] > 0){
                    $result = $this->fileProcessor->saveToTmp(key($_FILES));
                    if($result){
                        $logoData['header_logo_src'][0] = $result;
                        $scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORES;
                        //$designConfigData = $this->configFactory->create($scope, $data['store_id'], $logoData);
                        $designConfigData = $this->configFactory->create($scope, $store->getStoreId(), $logoData);

                        $this->configStorage->save($designConfigData);
                        $this->reinitableConfig->reinit();
                        $this->reindexGrid();
                    }
                }

                if(isset($data['reseller_currency']) && $data['reseller_currency'] != '')
                {
                    $defaultCurrency = $data['reseller_currency'];
                    $scope = \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITES;
                    $this->configWriter->save('currency/options/default', $data['reseller_currency'], $scope, $scopeId = $website->getWebsiteId());
                    $this->configWriter->save('currency/options/allow', $data['reseller_currency'], $scope, $scopeId = $website->getWebsiteId());
                }

                $this->messageManager->addSuccess(__('The Reseller has been saved.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) 
                {
                    if (isset($importParams) && $importParams != '') {
                        return $resultRedirect->setPath('*/*/importproducts', ['import_id' => $this->getRequest()->getParam('import_id'), 'reseller_id' => $this->getRequest()->getParam('reseller_id')]);
                    } else {
                        return $resultRedirect->setPath('*/*/edit', ['reseller_id' => $model->getId(), '_current' => true]);
                    }
                }

                if (isset($importParams) && $importParams != '') {
                    return $resultRedirect->setPath('*/*/index');
                }

            }
            //return $resultRedirect->setPath('*/*/');
        } catch (\Magento\Framework\Exception\LocalizedException $e) 
        {
            $this->messageManager->addError($e->getMessage());
        } catch (\RuntimeException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something went wrong while saving the Reseller.'));
        }

        $this->_getSession()->setFormData($data);
        $importParams = $this->getRequest()->getParam('import_id');
        if (isset($importParams) && $importParams != '') {
                return $resultRedirect->setPath('*/*/importproducts', ['import_id' => $this->getRequest()->getParam('import_id'), 'reseller_id' => $this->getRequest()->getParam('reseller_id')]);
        } else {
            //return $resultRedirect->setPath('*/*/edit', ['reseller_id' => $model->getId()]);
            return $resultRedirect->setPath('*/*/index');

        }
    }

    public function addProductsToWebsites($websiteId, $productpoolId, $commissionType, $productCommission, $storeId) 
    {
        $websiteIdArray = array($websiteId);
        $actionModel = $this->_objectManager->get(\Magento\Catalog\Model\Product\Action::class);
        $productPool = $this->productsFactory;
        $productPool->addFieldToSelect('product_id');
        $productIds = array();
        $productPool->addFieldToFilter('productpool_id', array($productpoolId));
        
        $poolData = $productPool->getData();

        foreach ($productPool->getData() as $value) {
            $productIds[] = $value['product_id'];
        }
        $websiteProducts = $this->_objectManager->get(\Magento\Catalog\Model\Product::class)
                ->getCollection()
                ->addAttributeToSelect("*")
                ->addWebsiteFilter($websiteId);

        $websiteProductIds = $websiteProducts->getAllIds();
        $websiteAddData = array_diff($productIds, $websiteProductIds);
        $websiteRemoveData = array_diff($websiteProductIds, $productIds);

        // Reseller Product price change
        if($productIds):
            $productIdsSet = implode(",", $productIds);
        endif;

        $resellerMainWebsiteId = $this->_resellerHelper->getResellerWebsiteId();
        $productCollection = $this->productCollection->create();
        $productCollection->addFieldToFilter('entity_id', array('in' => $productIdsSet));
        //$productCollection->addStoreFilter($storeId);
        $productCollection->addWebsiteFilter($resellerMainWebsiteId);
        $productCollection->addAttributeToSelect('*');

        foreach ($productCollection as $key => $_product) {

            if($commissionType == self::PERCENTAGE): // Percentage price calculation
                $_websiteIds = $_product->getWebsiteIds();
                
                if(in_array($websiteId, $_websiteIds)):
                    continue;
                endif;
               
                $_price = $_product->getPrice();
                $commission = $_price * $productCommission / 100;
                $finalPrice = $_price + $commission;
                                
				if(isset($_price) && $_price != ''):
                    $updateAttributesData = ['product_commission' => $productCommission, 'commission_type' => $commissionType, 'price' => $finalPrice, 'cost' => $_price];
                    $this->_objectManager->get('Magento\Catalog\Model\Product\Action')->updateAttributes([$_product->getId()], $updateAttributesData, $storeId);
                endif;
            endif;

            if($commissionType == self::FIXED): // Fixed price calculation
                $_websiteIds = $_product->getWebsiteIds();
                if(in_array($websiteId, $_websiteIds)):
                    continue;
                endif;
                
                $_price = $_product->getPrice();
                $finalPrice = $_price + $productCommission;

                if(isset($_price) && $_price != ''):
                	$updateAttributesData = ['product_commission' => $productCommission, 'commission_type' => $commissionType, 'price' => $finalPrice, 'cost' => $_price];
					$this->_objectManager->get('Magento\Catalog\Model\Product\Action')->updateAttributes([$_product->getId()], $updateAttributesData, $storeId);
				endif;
            endif;
        }
        
        if(is_array($websiteAddData) && !empty($websiteAddData)){
            $actionModel->updateWebsites($websiteAddData, $websiteIdArray, 'add');
        }
        if(is_array($websiteRemoveData) && !empty($websiteRemoveData)){
            $actionModel->updateWebsites($websiteRemoveData, $websiteIdArray, 'remove');
        }
        //die('8888');
        $indexerIds = array(
            'catalog_category_product',
            'catalog_product_category',
            'catalog_product_price',
            'catalog_product_attribute',
            'cataloginventory_stock',
            'catalogrule_product',
            'catalogsearch_fulltext',
        );
        foreach ($indexerIds as $indexerId) {
            $indexer = $this->indexerFactory->create();
            $indexer->load($indexerId);
            $indexer->reindexAll();
        }
    }
    
    protected function _getAdminUserData(array $data)
    {
        if (isset($data['password']) && $data['password'] === '') {
            unset($data['password']);
        }
        if (!isset($data['password'])
            && isset($data['password_confirmation'])
            && $data['password_confirmation'] === ''
        ) {
            unset($data['password_confirmation']);
        }
        return $data;
    }

    protected function reindexGrid()
    {
        $this->indexerRegistry->get(DesignConfig::DESIGN_CONFIG_GRID_INDEXER_ID)->reindexAll();
    }

    protected function _validate($data){
        $errorMessage = '';
        $storeCode = $data['store_code'];

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
