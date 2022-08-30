<?php

namespace Designnbuy\JobManagement\Block\Adminhtml\Grid\Column;

use Designnbuy\Vendor\Model\ResourceModel\User\CollectionFactory as VendorCollectionFactory;

/**
 * Admin Writer book grid statuses
 */
class Vendors extends \Magento\Backend\Block\Widget\Grid\Column
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
        VendorCollectionFactory $vendorCollectionFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Designnbuy\Vendor\Model\UserFactory $userFactory,
        \Designnbuy\Vendor\Helper\Data $vendorHelper,
        \Magento\Authorization\Model\RoleFactory $roleFactory,
        \Designnbuy\Vendor\Model\Product\Attribute\Source\Vendor  $vendorUsers,
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
    }
    
    public function getFrameCallback()
    {
        return [$this, 'decorateVendor'];
    }

    public function decorateVendor($value, $row, $column, $isExport)
    {
        $_item = $row->getItemId();
        $vendors = $this->getVendors();        
        if($this->isSuperAdmin()): 
        $cell = "";
        $cell = '<div class="admin__field-control">';
            $cell .='<select class="admin__control-select" id="item_vendor_'.$_item.'" name="item_vendor_'.$_item.'">';
                foreach($vendors as $vendor):
                    if($row->getVendorId() == $vendor['value']):
                        $select = 'selected="selected"';
                    else:
                        $select = '';
                    endif; 
                    $cell .='<option value="'.$vendor['value'].'" '.$select.'> '.$vendor['label'].'</option>';
                endforeach;
            $cell .='</select>';
        $cell .='</div>';
        endif;
        return $cell;
    }
}
