<?php
namespace Designnbuy\Reseller\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Data extends AbstractHelper
{

    protected $_objectManager;
    protected $_reseller;

    public function __construct(
        Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Designnbuy\Reseller\Model\Resellers $resellers,
        \Magento\Catalog\Model\ProductFactory $product,
        \Magento\User\Model\UserFactory $userFactory
    ) {
        $this->_scopeConfig = $context;
        parent::__construct($context);        
        $this->_objectManager = $objectManager;
        $this->_storeManager = $storeManager;
        $this->_reseller = $resellers;
        $this->_product = $product;
        $this->_userFactory = $userFactory;
    }

    public function createResellerRole($roleId,$userName){
        $resellerRole = $this->_objectManager->create('Magento\Authorization\Model\Role')->load($roleId);
        $defaultRole = $resellerRole->getData();

        unset($defaultRole['role_id']);

        $newRole = $this->_objectManager->create('Magento\Authorization\Model\Role')
                        ->setData($defaultRole)
                        ->setRoleName($userName)
                        ->save();

        $resellerRule = $this->_objectManager->create('Magento\Authorization\Model\Rules')
                             ->getCollection()
                             ->addFieldToSelect('resource_id')
                             ->addFieldToFilter('role_id', (int)$roleId)
                             ->addFieldToFilter('permission','allow');

        $resource = array_column($resellerRule->getData(),'resource_id');

        $newResellerRule = $this->_objectManager->create('Magento\Authorization\Model\Rules')
                                ->setRoleId($newRole->getId())
                                ->setResources($resource)
                                ->saveRel();
        return $newRole->getId();
    }
    
    public function updateResellerRole($roleId,$newRoleId,$websiteId){

        $webSiteRule = $this->_objectManager->create('Amasty\Rolepermissions\Model\Rule')->loadByRole($roleId);
        $websiteData = $webSiteRule->getData();

        unset($websiteData['id']);
        unset($websiteData['scope_storeviews']);
        if($websiteData['categories'])$websiteData['categories']= implode(',',$websiteData['categories']);
        if($websiteData['products'])$websiteData['products'] = implode(',',  $websiteData['products']);
        $websiteData['role_id'] = $newRoleId;
        $websiteData['scope_websites'] = $websiteId;

        $newWebSite = $this->_objectManager->create('Amasty\Rolepermissions\Model\Rule')
                           ->addData($websiteData)
                           ->save();
        return $newWebSite;
    }


    public function isResellerUser($userid)
    {
        if($userid){
            $reseller = $this->_reseller->getCollection()->addFieldToFilter('user_id',$userid)->getFirstItem();
            if(count($reseller->getData()))
            {
                return $reseller->getId();
            }
            return false;
        }
        return false;
    }

    public function getResellerWebsite($resellerId){
        if($resellerId){
            $reseller = $this->_reseller->load($resellerId);
            $websiteId = $reseller->getWebsiteId();
            return $websiteId;
        }
      return false;
    }

    public function isResellerStore($websiteId){

        $reseller = $this->_reseller->getCollection()->addFieldToFilter('website_id',$websiteId);

        if(count($reseller->getData()))
        {
            return true;
        }
        return false;
    }

    public function getResellerWebsiteId()
    {
        return $this->getConfig('reseller_settings/reseller_role/default_website');
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

    public function getProductCommission($_product, $itemPrice)
    {
        //$storeId = $this->_storeManager->getStore()->getId();
        $profit = 0;
        $websiteId = $this->_storeManager->getStore()->getWebsiteId();
        $mainWebsiteId = $this->getResellerWebsiteId();
        if($websiteId == $mainWebsiteId) {
            return $profit;
        }

        if($itemPrice) {
            $price = $itemPrice;
        }else {
            $price = $_product->getPrice();
        }

        $commissionType = $_product->getAttributeText('commission_type');
        $productCommission = $_product->getProductCommission();
        
        if($productCommission) {
            if($productCommission != NULL && $productCommission > 0) {
                if($commissionType == 'Percentage') {
                    $profit = ($price * $productCommission)/100;
                } else {
                    $profit = $productCommission;
                }
                return $profit;
            } else {
                return $profit;
            }
        }
    }

    /* @13 check resellerUser Status*/
    public function getResellerUserStatus($userId)
    {
        $vendorUser = $this->_userFactory->create()->load($userId);
        return $vendorUser->getIsActive();
    }
}
