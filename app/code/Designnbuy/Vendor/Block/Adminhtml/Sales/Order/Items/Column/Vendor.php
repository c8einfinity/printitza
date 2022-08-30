<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Vendor\Block\Adminhtml\Sales\Order\Items\Column;

use Designnbuy\Vendor\Model\ResourceModel\User\CollectionFactory as VendorCollectionFactory;

/**
 * Sales Order items name column renderer
 */
class Vendor extends \Magento\Sales\Block\Adminhtml\Items\Column\DefaultColumn
{
    /**
     * @param VendorCollectionFactory $vendorCollectionFactory
     */
    protected $vendorCollectionFactory;

    protected $authSession;

    protected $userFactory;

    /**
     * Factory for user role model
     *
     * @var \Magento\Authorization\Model\RoleFactory
     */
    protected $_roleFactory;

    protected $vendorHelper;

    protected $authorizationRoleCollection;


    protected $vendorUsers;

    /**
     * Admin helper
     *
     * @var \Magento\Sales\Helper\Admin
     */
    protected $_adminHelper;


    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\Product\OptionFactory $optionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\Product\OptionFactory $optionFactory,
        VendorCollectionFactory $vendorCollectionFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Designnbuy\Vendor\Model\UserFactory $userFactory,
        \Designnbuy\Vendor\Helper\Data $vendorHelper,
        \Magento\Authorization\Model\RoleFactory $roleFactory,
        \Designnbuy\Vendor\Model\Product\Attribute\Source\Vendor  $vendorUsers,
        \Magento\Authorization\Model\ResourceModel\Role\Collection $authorizationRoleCollection,
        \Magento\Authorization\Model\Acl\AclRetriever $aclRetriever,
        \Magento\Sales\Helper\Admin $adminHelper,
        array $data = []
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
        parent::__construct($context, $stockRegistry, $stockConfiguration, $registry, $optionFactory, $data);
    }


    public function getSubmitUrl()
    {
        $item = $this->getItem()->getId();
        return $this->getUrl('designnbuy_vendor/*/changeVendor', ['item_id' => $this->getItem()->getId()]);
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
        //$currentVendorRole = $this->getCurrentUserVendorRole();
        $currentVendorUser = $this->getCurrentUser();

        if($currentVendorUser->getRole()->getRoleId() == $defaultRoleId){
            $vendors[] = array('value' => $currentVendorUser->getId(), 'label' => $currentVendorUser->getName());
        } else {
            $vendors = $this->vendorUsers->getAllOptions();
        }

        return $vendors;
    }

    public function getVendorCommission()
    {
        $item = $this->getItem();
        $commission = $this->vendorHelper->getVendorCommission($item);

        $commissionPrice = $this->_adminHelper->displayPrices(
            $this->getOrder(),
            $commission,
            $commission,
            true,
            ''
        );
        return $commissionPrice;
    }

    public function isSuperAdmin()
    {
        $role = $this->getCurrentUser()->getRole();
        $resources = $this->aclRetriever->getAllowedResourcesByRole($role->getId());
        $resource = 'Designnbuy_Vendor::vendor';
        return in_array("Magento_Backend::all", $resources);

        //return $this->vendorHelper->isVendor();
    }

}
