<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Designnbuy\Reseller\Observer;

use Magento\Framework\Event\ObserverInterface;

class SaveResellerProductPrice implements ObserverInterface
{
    /** @var \Amasty\Rolepermissions\Helper\Data */
    protected $helper;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_authSession;

    protected $aclRetriever;
    protected $_storeManager;


    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Authorization\Model\Acl\AclRetriever $aclRetriever,
        \Magento\Framework\AuthorizationInterface $authorization,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Designnbuy\Reseller\Model\Admin $reseller
    ) {
        $this->_objectManager = $objectManager;
        $this->aclRetriever = $aclRetriever;
        $this->_request = $request;
        $this->_storeManager = $storeManager;
        $this->_authorization = $authorization;
        $this->_authSession = $authSession;
        $this->_reseller = $reseller;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->_request->getModuleName() == 'api'){
            return;
        }
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $observer->getProduct();

        /* compare product price with main store price */
        $_productId = $observer->getProduct()->getId();
        $storeId = $this->_storeManager->getDefaultStoreView()->getStoreId();
        $_mainstoreProduct = $this->_objectManager->get('Magento\Catalog\Model\Product')->setStoreId($storeId)->load($_productId);
        $_mainstorePrice = $_mainstoreProduct->getPrice();
        $product->setCost($_mainstorePrice);
        
        if($this->_reseller->isResellerAdmin()) {
            //$_mainstoreSpecialPrice = $_mainstoreProduct->getSpecialPrice();
            $productCommission = ($product->getProductCommission() != '') ? $product->getProductCommission() : $this->_reseller->getResellerCommission();
            $productCommissionType = ($product->getCommissionType() != '') ? $product->getCommissionType() : $this->_reseller->getResellerCommissionType();
            if ($productCommission) {
                if ($productCommissionType == 1) {
                    $productCommission = $_mainstorePrice + ($productCommission / 100) * $_mainstorePrice;
                } else {
                    $productCommission = $_mainstorePrice + $productCommission;
                }
                $product->setPrice($productCommission);
            }
            /*if ($product->getProductCommission()) {
                $productCommission = 0;
                if ($product->getCommissionType() == 1) {
                    $percentage = $product->getProductCommission();
                    $productCommission = $_mainstorePrice + ($percentage / 100) * $_mainstorePrice;
                } else {
                    $productCommission = $_mainstorePrice + $product->getProductCommission();
                }
                $product->setPrice($productCommission);
            }*/

            /* reseller only able to edit product price,name,qty,status */
            /*$unsetattribute = array("name", "qty", "sku","status","type_id","entity_id","old_id","price","special_price","description","short_description","special_from_date","special_to_date","cost","tier_price");
            $attributes = $product->getAttributes();
            foreach ($attributes as $attribute) {
                $code = $attribute->getAttributeCode();
                if (in_array($code, $unsetattribute)){

                }else{
                    $product->unsetData($code);
                }
            }*/
        } 

    }
}
