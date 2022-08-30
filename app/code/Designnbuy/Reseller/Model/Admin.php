<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Reseller\Model;

/**
 * Permissions checker
 *
 */
class Admin extends \Magento\Framework\DataObject
{
    /**
     * Store ACL role model instance
     *
     * @var \Magento\Authorization\Model\Role
     */
    protected $_adminRole;

    /**
     * Storage for disallowed entities and their ids
     *
     * @var array
     */
    protected $_disallowedWebsiteIds = [];

    /**
     * @var array
     */
    protected $_disallowedStores = [];

    /**
     * @var array
     */
    protected $_disallowedStoreIds = [];

    /**
     * @var array
     */
    protected $_disallowedStoreGroupIds = [];

    /**
     * @var array
     */
    protected $_disallowedStoreGroups = [];

    /**
     * Storage for categories which are used in allowed store groups
     *
     * @var array
     */
    protected $_allowedRootCategories;

    /**
     * Storage for categories which are not used in
     * disallowed store groups
     *
     * @var array
     */
    protected $_exclusiveRootCategories;

    /**
     * Storage for exclusive checked categories
     * using category path as key
     * @var array
     */
    protected $_exclusiveAccessToCategory = [];


    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $backendAuthSession;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\User\Model\UserFactory $userFactory,
        \Magento\Backend\Model\Auth\Session $backendAuthSession,
        \Designnbuy\Reseller\Model\ResellersFactory $resellerFactory,
        array $data = []
    ) {
        $this->_storeManager = $storeManager;
        $this->userFactory = $userFactory;
        $this->backendAuthSession = $backendAuthSession;
        $this->resellerFactory = $resellerFactory;
        parent::__construct($data);
    }


    public function getAdminRole()
    {
        if($this->backendAuthSession->getUser() && $this->backendAuthSession->getUser()->getUserId()){
            return $this->userFactory->create()->load($this->backendAuthSession->getUser()->getUserId())->getRole();
        }
        return;
        //return $this->backendAuthSession->getUser()->getRole();
    }

    public function getAdminUser()
    {
        if($this->backendAuthSession->getUser() && $this->backendAuthSession->getUser()->getUserId()){
            return $this->userFactory->create()->load($this->backendAuthSession->getUser()->getUserId());
        }
        return;
    }

    public function getResellerAdmin()
    {
        $user = $this->getAdminUser();
        if($user){
            $userId = $user->getId();
            $reseller = $this->resellerFactory->create()->load($userId,'user_id');
            return $reseller;
        }
        return;
    }

    public function getResellerByRole()
    {
        $role = $this->getAdminRole();
        if($role){
            $roleId = $role->getRoleId();
            $reseller = $this->resellerFactory->create()->load($roleId,'role_id');
            return $reseller;
        }
        return;
    }


    public function getStoreIds()
    {
        $store[] = $this->getResellerAdmin()->getStoreId();
        return $store;
    }

    /**
     * Get website ids of allowed store groups
     *
     * @return array
     */
    public function getRelevantWebsiteIds()
    {
        $websiteIds[] = $this->getResellerAdmin()->getWebsiteId();
        return $websiteIds;
    }

    /**
     * Get allowed store group ids from core admin role object
     *
     * If role model is not defined yet use default value as empty array.
     *
     * @return array
     */
    public function getStoreGroupIds()
    {

        $storeGroup[] = $this->getResellerAdmin()->getGroupId();
        return $storeGroup;
    }

    public function isResellerAdmin()
    {
        $reseller =  $this->getResellerAdmin();
        if($reseller && $reseller->getId()){
            return true;
        }
        return false;
    }

    public function getResellerCommission()
    {
        $reseller =  $this->getResellerAdmin();
        $resellecrCommission = $reseller->getProductCommission();
        return $resellecrCommission;
    }

    public function getResellerCommissionType()
    {
        $reseller =  $this->getResellerAdmin();
        $resellecrCommissionType = $reseller->getCommissionType();
        return $resellecrCommissionType;
    }

}
