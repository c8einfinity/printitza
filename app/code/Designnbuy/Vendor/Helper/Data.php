<?php
/**
 * Customer attribute data helper
 */

namespace Designnbuy\Vendor\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $authSession;

    protected $userFactory;

    protected $roleFactory;

    protected $_productCollectionFactory;

    protected $_productFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Designnbuy\Vendor\Model\UserFactory $userFactory,
        \Designnbuy\Workflow\Model\RoleFactory $roleFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig

    ) {
        parent::__construct($context);
        $this->authSession = $authSession;
        $this->userFactory = $userFactory;
        $this->roleFactory = $roleFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_productFactory = $productFactory;
        $this->_scopeConfig = $scopeConfig;
    }

    public function getProductVendorGroup()
    {
        
    }

    public function getCurrentUser()
    {
        return $this->authSession->getUser();
    }

    public function isVendor()
    {
        if($this->getVendorUser() && $this->getVendorUser()->getId()){
            return true;
        } else {
            return false;
        }
    }

    public function getVendorUser()
    {
        $user = $this->getCurrentUser();
        if($user){
            $vendorUser = $this->userFactory->create()->loadByUserId($user->getUserId());
            if($vendorUser->getUserId()) {
                return $vendorUser;
            }
        }
        return false;
    }

    public function getVendorUserRole()
    {
        $vendorUser = $this->getVendorUser();
        if($vendorUser){
            return $this->roleFactory->create()->load($vendorUser->getRoleId());
        }
        return false;
    }

    public function getVendorUserViewStatuses()
    {
        $vendorRole = $this->getVendorUserRole();
        if($vendorRole){
            return $vendorRole->getViewStatus();
        }
        return false;
    }

    public function getVendorUserUpdateStatuses()
    {
        $vendorRole = $this->getVendorUserRole();
        if($vendorRole){
            return $vendorRole->getUpdateStatus();
        }
        return false;
    }

    public function getVendorRoleId()
    {
        return $this->_scopeConfig->getValue(
            'dnbvendor/settings/default_role',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getVendorProductCollection()
    {
        $defaultRoleId = $this->getVendorRoleId();
        $user = $this->getCurrentUser();
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        if ($user->getRole()->getRoleId() == $defaultRoleId){
            $collection->addAttributeToFilter('vendor_id', $user->getUserId());
        }

        return $collection->getAllIds();
    }


    public function getVendorCommission($item) {

        $product_id = $item->getProductId();

        $product = $item->getProduct();
        //$product = $this->_productFactory->create()->load($product->getId());
        if (!is_null($product)) {
            $productOptions = $item->getProductOptions();
            $infoBuyRequest = $productOptions['info_buyRequest'];
            $printingPrice = 0;
            /*if($infoBuyRequest['printingMethod']){
                $printingMethodData = Mage::helper('core')->jsonDecode($infoBuyRequest['printingMethod']);
                $printingPrices = Mage::getModel('design/design')->calculatePrintingMethodPrice($printingMethodData, $item->getQtyOrdered());
                $printingPrice = $printingPrices['printingPrice'];
            }*/
            $productVendorId = $product->getVendorId();
            $itemVendorId = $item->getVendorId();
            if($itemVendorId == $productVendorId){
                $vendorId = $productVendorId;
            }else{
                $vendorId = $itemVendorId;
            }
            $vendorUser = $this->userFactory->create()->loadByUserId($vendorId);

            if (!is_null($vendorUser)) {
                $commission_percentage = $vendorUser->getCommissionPercentage();
                if($itemVendorId == $productVendorId || $product->getVendorCommission() != ''){
                    if($product->getVendorCommission() != ''){
                        $commission_percentage = $product->getVendorCommission();
                    }
                }
                if($item->getTaxPercent() > 0){
                    $printingPrice = $printingPrice + ($printingPrice * $item->getTaxPercent() / 100);
                }
                $vendor_amount = ((($item->getPriceInclTax()  * $item->getQtyOrdered()) - $printingPrice) * $commission_percentage) / 100;
                return $vendor_amount;
            }
        }
        return 0;
    }
}
