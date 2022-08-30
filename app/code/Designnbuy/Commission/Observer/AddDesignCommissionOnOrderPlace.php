<?php

namespace Designnbuy\Commission\Observer;

use Magento\Framework\ObjectManager\ObjectManager;

class AddDesignCommissionOnOrderPlace implements \Magento\Framework\Event\ObserverInterface
{
    /** * @const string */
    const COMMISSION_FIXED = 1;
    
    const COMMISSION_PERCENTAGE = 2;

    const DESIGNER_LEVEL = 1;

    const COMMISSION_FOR = 0;

    const COMMISSION_FOR_PRODUCT = 1;

    const RESELLER_TYPE = 1;

    protected $_logger;
    
    protected $_objectManager;
    
    protected $_orderFactory;

    protected $_checkoutSession;

    protected $_productOptions  = null;

    protected $_productAttributeSetId  = null;

    protected $_product;

    protected $_item;

    protected $_order;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    
    /**
     * request Interface
     *
     * @var \Magento\Framework\App\RequestInterface
     */
    
    protected $_request;

    protected $_customer;
    
    public function __construct(        
        \Psr\Log\LoggerInterface $loggerInterface,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\ObjectManager\ObjectManager $objectManager,
        \Magento\Sales\Model\Order\ItemFactory $orderItemFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Customer\Model\Customer $customer,
        //\Designnbuy\Designer\Model\DesignerFactory $designerFactory,
        \Designnbuy\Designidea\Model\DesignideaFactory $designideaFactory,
        \Designnbuy\Template\Model\TemplateFactory $templateFactory,
        \Designnbuy\Commission\Model\CommissionFactory $commissionFactory,
       // \Designnbuy\Designer\Model\LevelFactory $designerLevelFactory,
        \Designnbuy\Reseller\Model\Resellers $reseller,
        \Designnbuy\Reseller\Helper\Data $resellerHelper,
        \Magento\User\Model\UserFactory $userFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory
    ) {
        $this->_logger = $loggerInterface;
        $this->_objectManager = $objectManager;        
        $this->_orderFactory = $orderFactory;
        $this->_checkoutSession = $checkoutSession;
        $this->_request = $request;
        $this->_logger = $logger;
        $this->_orderItemFactory = $orderItemFactory;
        $this->_storeManager = $storeManager;
        $this->_customer = $customer;
        //$this->_designerFactory = $designerFactory;
        $this->_designideaFactory = $designideaFactory;
        $this->_templateFactory = $templateFactory;
        $this->_commissionFactory = $commissionFactory;
        //$this->_designerLevelFactory = $designerLevelFactory;
        $this->_reseller = $reseller;
        $this->_resellerHelper = $resellerHelper;
        $this->_userFactory = $userFactory;
        $this->_productFactory = $productFactory;
    }
    
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /* @var $order \Magento\Sales\Model\Order */
        $orderIds = $observer->getEvent()->getOrderIds();
        if (count($orderIds)) {
            $orderId = $orderIds[0];
            $order = $this->_orderFactory->create()->load($orderId);
            if($order){
                // Get the items from the order
                $items = $order->getAllVisibleItems();
                foreach ($items as $item) {
                    $this->addCommissinManagementRecord($order->getIncrementId(), $item);
                }
            }
            return $this;            
        }
    }

    public function addCommissinManagementRecord($orderId, $item)
    {
        $itemId = $item->getId();
        $_item = $this->_orderItemFactory->create()->load($itemId);
        $_product = $_item->getProduct();

        $this->_productAttributeSetId = $_product->getAttributeSetId();
        $productOptions = $_item->getProductOptions();
        
        $itemPurchasedId = null;

        if(isset($productOptions['info_buyRequest']['designer_id']) && isset($productOptions['info_buyRequest']['designer_id']))
        {
            $userId = $productOptions['info_buyRequest']['designer_id'];
            if($userId != ''){
                //$designer = $this->getDesigner($userId); // Designer Customer Id
                //$customerId = $designer->getCustomerId();
                //$_customer = $this->getCustomerInformation($customerId);
                
                //$userName = $designer->getFirstName() . ' ' . $designer->getLastName();
                $userName = '';
                $userType = 2;//$_customer->getCustomertype();
                $itemId = $productOptions['info_buyRequest']['designer_design_id'];
                /*DesignId as Itemid*/
                $toolType = '';
                if(isset($productOptions['info_buyRequest']['toolType']) && isset($productOptions['info_buyRequest']['toolType'])) {
                    $toolType = $productOptions['info_buyRequest']['toolType'];
                    if ($toolType == 'web2print') {
                        $_design = $this->_templateFactory->create();
                        $_design->load($itemId);
                    } else {
                        $_design = $this->_designideaFactory->create();
                        $_design->load($itemId);
                    }
                }

                $itemName = $_design->getTitle();
                
                /*ProductId as itemPurchasedId*/
                $itemPurchasedId = $productOptions['info_buyRequest']['product'];
                $_itemPrice = $item->getPriceInclTax();
                $itemQty = $item->getQtyOrdered();

                $_commissionAmount = $this->designCommissionCalculation($userId, $itemId, $itemQty, $_itemPrice, $toolType);
                
                $commissionfor = self::COMMISSION_FOR;
                $currentStoreId = $this->getStoreId();
                
                /**  Save commission*/
                $_commission = $this->_commissionFactory->create();
                //$_commission->setUserId($customerId);
                $_commission->setOrderId($orderId); 
                $_commission->setUserName($userName);
                $_commission->setItemId($itemId);
                $_commission->setItemName($itemName);
                $_commission->setItemQty($itemQty);
                $_commission->setItemPurchasedId($itemPurchasedId);
                $_commission->setUserType($userType);
                $_commission->setCommissionAmount($_commissionAmount);
                $_commission->setCommissionFor($commissionfor);
                $_commission->setStoreId($currentStoreId);
                $_commission->save();    
            }
        }

        /*Reseller Commission calculation*/
        $websiteId = $this->_storeManager->getStore()->getWebsiteId();
        $reseller = $this->_reseller->load($websiteId,'website_id');
        if($reseller->getResellerId())
        {
            $productId = $_product->getProductId();
            $itemName = $item->getName();
            
            if(isset($productOptions['info_buyRequest']['product'])) {
                $itemPurchasedId = $productOptions['info_buyRequest']['product'];
            }
            $_price = $item->getPriceInclTax();
            $_cost = $_product->getCost();

            if($_product->getTypeId() == 'configurable') 
            {
                $currentStoreId = $this->getStoreId();
                $_product = $this->_productFactory->create()->loadByAttribute('sku', $item->getSku())->setStoreId($currentStoreId);

                $productId = $_product->getId();
                $itemPurchasedId = $productId;
                $itemName = $_product->getName();
                //$_price = $_product->getPriceInclTax();
                $_price = $_product->getPrice();
                $_cost  = $_product->getCost();
            }
            $commission = 0;
            //$resellerCommission = $this->_resellerHelper->getProductCommission($_product, $item->getPriceInclTax());
            
            if($_cost):
                $resellerCommission = $_price - $_cost;
            else:
                return;
            endif;

            $itemQty = $item->getQtyOrdered();
            $_commissionAmount = $resellerCommission * $itemQty;
            $userName = $this->getResellerUserName($reseller->getUserId());
            $userId = $reseller->getUserId();

            $commissionfor = self::COMMISSION_FOR_PRODUCT;
            $currentStoreId = $this->getStoreId();
            $itemId     = NULL;

            /**  Save commission*/
            $_commission = $this->_commissionFactory->create();
            $_commission->setUserId($userId);
            $_commission->setOrderId($orderId); 
            $_commission->setUserName($userName);
            $_commission->setItemId($itemId);
            $_commission->setItemName($itemName);
            $_commission->setItemQty($itemQty);
            $_commission->setItemPurchasedId($itemPurchasedId);
            $_commission->setUserType(self::RESELLER_TYPE);
            $_commission->setCommissionAmount($_commissionAmount);
            $_commission->setCommissionFor($commissionfor);
            $_commission->setStoreId($currentStoreId);
            $_commission->save();
        }
    }

    /** Return commission calculation amount based on design id and qty */
    public function designCommissionCalculation($userId, $itemId, $itemQty, $_itemPrice, $toolType)
    {
        $designId = $itemId;
        //$_designer = $this->_designerFactory->create();
        //$_designer->load($userId);
        //$designerCommissionType = $_designer->getCommissionType();
        //$designerLevelId = $_designer->getDesignerLevel();
        $designerCommissionType = '';
        if($designerCommissionType == self::DESIGNER_LEVEL){
           // return $this->designerLevelCommissionCalculation($designerLevelId, $designId, $itemQty, $_itemPrice);
        } else {
            if($toolType == 'web2print'){
                return $this->templateCommissionCalculation($designId, $itemQty, $_itemPrice);
            } else {
                return $this->designIdeaCommissionCalculation($designId, $itemQty, $_itemPrice);
            }
        }

        /**Manage Commission by DESIGNER Level*/
        /*if($designerCommissionType == self::DESIGNER_LEVEL){
            return $this->designerLevelCommissionCalculation($designerLevelId, $designId, $itemQty, $_itemPrice);
        } else {
            // calculate commission on DESIGN level
            return $this->designIdeaCommissionCalculation($designId, $itemQty, $_itemPrice);
        }*/
    }

    /** IF Commission manage by designer level, commission calculation */
    /*public function designerLevelCommissionCalculation($designerLevelId, $designId, $itemQty, $_itemPrice)
    {
        $_designerLevel = $this->_designerLevelFactory->create();
        $_designerLevel->load($designerLevelId);
        $commission = $_designerLevel->getLevel();
        return $this->percentageCommissionCalculation($_itemPrice, $itemQty, $commission);
        /*$designCommissionType = $this->getDesignCommissionType($designId);
        if($designCommissionType == self::COMMISSION_FIXED){
           return $this->fixedCommissoinCalculation($itemQty, $commission);
        }else {
           return $this->percentageCommissionCalculation($_itemPrice, $itemQty, $commission);
        }
    }*/

    /** DesignIdea Design Commissin Calculation */
    public function designIdeaCommissionCalculation($designId, $itemQty, $_itemPrice)
    {
        $_designIdea = $this->_designideaFactory->create();
        $_designIdea->load($designId);
        $designCommissionType = $_designIdea->getCommissionType();
        $commission = $_designIdea->getCommission();

        if($designCommissionType == self::COMMISSION_FIXED) {
            return $this->fixedCommissoinCalculation($itemQty, $commission);
        }else {
            return $this->percentageCommissionCalculation($_itemPrice, $itemQty, $commission);
        }
    }

    /** DesignIdea Design Commissin Calculation */
    public function templateCommissionCalculation($designId, $itemQty, $_itemPrice)
    {
        $_template = $this->_templateFactory->create();
        $_template->load($designId);
        $designCommissionType = $_template->getCommissionType();
        $commission = $_template->getCommission();

        if($designCommissionType == self::COMMISSION_FIXED) {
            return $this->fixedCommissoinCalculation($itemQty, $commission);
        }else {
            return $this->percentageCommissionCalculation($_itemPrice, $itemQty, $commission);
        }
    }

    /** Percentage Commissoin Calculation */
    public function percentageCommissionCalculation($_itemPrice, $itemQty, $commission){
        return $commissionCalculation = ($_itemPrice * $itemQty) * $commission / 100;
    }

    /** Fixed Commissoin Calculation */
    public function fixedCommissoinCalculation($itemQty, $commission) {
        return $commissionCalculation =  $itemQty * $commission;
    }

    /** Return Design Commission Type */
    /*public function getDesignCommissionType($designId)
    {
        $_designIdea = $this->_designideaFactory->create();
        $_designIdea->load($designId);
        $designCommissionType = $_designIdea->getCommissionType();
        return $designCommissionType;
    }*/

    /** Return current store id */
    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    /** Return Customer information based on selected design's designerId */
    public function getCustomerInformation($customerId) {
        $_customer = $this->_customer;
        return $_customer->load($customerId);
    }

   /* public function getDesigner($designerId){
        $_designer = $this->_designerFactory->create();
        $_designer->load($designerId);
        return $_designer;
        /*$CustomerId = $_designer->getCustomerId();
        return $CustomerId;
    }*/

    /* Get Reseller User Fullname*/
    protected function getResellerUserName($resellerId) {
        //$reseller = $this->_reseller()->load($resellerId);
        $reseller = $this->_userFactory->create()->load($resellerId);
        $resellerName = $reseller->getFirstname().' '.$reseller->getLastname();
        return $resellerName;
    }
}

