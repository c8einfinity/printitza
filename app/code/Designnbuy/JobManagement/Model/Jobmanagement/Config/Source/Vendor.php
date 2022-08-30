<?php

namespace Designnbuy\JobManagement\Model\Jobmanagement\Config\Source;

use Designnbuy\Vendor\Model\ResourceModel\User\CollectionFactory as VendorCollectionFactory;
/**
 * Used in creating options for commission type config value selection
 */

class Vendor implements \Magento\Framework\Option\ArrayInterface
{
    protected $vendorCollectionFactory;

    protected $authSession;

    protected $userFactory;

    protected $_roleFactory;

    protected $vendorHelper;

    protected $authorizationRoleCollection;

    protected $vendorUsers;

    protected $_adminHelper;

    protected $options;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param VendorCollectionFactory $vendorCollectionFactory
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Designnbuy\Vendor\Model\UserFactory $userFactory
     * @param \Designnbuy\Vendor\Helper\Data $vendorHelper
     * @param \Magento\Authorization\Model\RoleFactory $roleFactory
     * @param \Designnbuy\Vendor\Model\Product\Attribute\Source\Vendor $vendorUsers
     * @param \Magento\Authorization\Model\ResourceModel\Role\Collection $authorizationRoleCollection
     * @param \Magento\Authorization\Model\Acl\AclRetriever $aclRetriever,
     * @param \Magento\Sales\Helper\Admin $adminHelper
     * @param array $data
     */

    public function __construct(
        VendorCollectionFactory $vendorCollectionFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Designnbuy\Vendor\Model\UserFactory $userFactory,
        \Designnbuy\Vendor\Helper\Data $vendorHelper,
        \Magento\Authorization\Model\RoleFactory $roleFactory,
        \Designnbuy\Vendor\Model\Product\Attribute\Source\Vendor $vendorUsers,
        \Magento\Authorization\Model\ResourceModel\Role\Collection $authorizationRoleCollection,
        \Magento\Authorization\Model\Acl\AclRetriever $aclRetriever,
        \Magento\Sales\Helper\Admin $adminHelper
    ) {
        $this->vendorCollectionFactory = $vendorCollectionFactory;
        $this->authSession = $authSession;
        $this->userFactory = $userFactory;
        $this->vendorHelper = $vendorHelper;
        $this->_roleFactory = $roleFactory;
        $this->vendorUsers = $vendorUsers;
        $this->authorizationRoleCollection = $authorizationRoleCollection;
        $this->aclRetriever = $aclRetriever;
        $this->_adminHelper = $adminHelper;
    }


    public function getCurrentUser()
    {
        return $this->authSession->getUser();
    }

    public function getCurrentVendorUser()
    {
        $vendorUser = $this->userFactory->create()->loadByUserId($this->getCurrentUser()->getUserId());
        if($vendorUser){
            return $vendorUser;
        }
    }

    public function getCurrentUserVendorRole()
    {
        $vendorUser = $this->getCurrentVendorUser();
        if($vendorUser){
            return $this->_roleFactory->create()->load($vendorUser->getRoleId());
        }
    }

    public function getVendors()
    {
        $defaultRoleId = $this->vendorHelper->getVendorRoleId();
        $vendors = [];
        $currentVendorUser = $this->getCurrentUser();

        if($currentVendorUser->getRole()->getRoleId() == $defaultRoleId){
            $vendors[] = array('value' => $currentVendorUser->getId(), 'label' => $currentVendorUser->getName());
        } else {
            $vendors = $this->vendorUsers->getAllOptions();
        }

        return $vendors;
    }

    public function isSuperAdmin()
    {
        $role = $this->getCurrentUser()->getRole();
        $resources = $this->aclRetriever->getAllowedResourcesByRole($role->getId());
        $resource = 'Designnbuy_Vendor::vendor';
        return in_array("Magento_Backend::all", $resources);
    }
    
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $vendors = $this->getVendors();
        if ($this->options === null) {
            foreach ($vendors as $vendor) {
                $this->options[] = ['value' => $vendor['value'], 'label' => $vendor['label'],];
            }        
        }
        return $this->options;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $array = [];
        foreach ($this->toOptionArray() as $item) {
            $array[$item['value']] = $item['label'];
        }
        return $array;
    }
}
