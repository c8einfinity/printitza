<?php
/**
* Copyright © 2018 Porto. All rights reserved.
*/

namespace Designnbuy\Pricecalculator\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;

use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    protected $_objectManager;

    protected $_mediaDirectory;

    protected $_scopeConfig;

    /**
     * @var GroupManagementInterface
     */
    protected $_groupManagement;

    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;
    
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Api\GroupManagementInterface $groupManagement,
        \Magento\Customer\Model\Session $customerSession
    ) {        
        $this->_objectManager = $objectManager;
        $this->_storeManager = $storeManager;
        $this->_scopeConfig = $scopeConfig;
        $this->_customerSession = $customerSession;
        $this->_groupManagement = $groupManagement;
        parent::__construct($context);
    }


    public function calculateCSVPrice($product, $itemOptionsData, $qty = 1)
    {
        $squareAreaPrice = 0;

        if($product->getEnableCustomHeightWidth() == 1) {
            $area = 1;
            $pricingCalculation = array();
            foreach ($itemOptionsData as $key => $value) {
                $pricingCalculation[$key] = $value;
            }


            foreach ($product->getOptions() as $key => $value) {
                if ($value->getType() == 'field' && ($value->getDesigntoolType() == 'width' || $value->getDesigntoolType() == 'height')) {
                    if (isset($pricingCalculation[$value->getOptionId()]) && !empty($pricingCalculation[$value->getOptionId()])) {
                        $area = $area * $pricingCalculation[$value->getOptionId()];
                    }
                }
            }

            $squareAreaPrice = $this->getSquareAreaPrice($area, $product);
        }
        return $squareAreaPrice;
    }

    public function getSquareAreaPrice($area, $_product)
    {
        //$allGroupsId = $this->getAllCustomerGroupsId();
        $prices = $_product->getSquareAreaPrice();
        //$prevGroup = $allGroupsId;

        $squareAreaPrice = 0;
        $custGroup = $this->_customerSession->getCustomerGroupId();
        foreach ($prices as $price) {
            /*if ($price['cust_group'] != $custGroup) {
                continue;
            }*/
            if ($price['price_qty'] <= $area) {
                $squareAreaPrice = $price['website_price'];
            } else {
                continue;
            }
        }

        return $squareAreaPrice;
    }

    /**
     * Gets the CUST_GROUP_ALL id
     *
     * @return int
     */
    protected function getAllCustomerGroupsId()
    {
        // ex: 32000
        return $this->_groupManagement->getAllCustomersGroup()->getId();
    }

    public function getIsEnabled()
    {
        return $this->_getConfigValue('orderattachment/attachmentgroup/enabled');
    }
    public function getConfigProductUnit()
    {
        return $this->_scopeConfig->getValue(
                'canvas/configuration/base_unit',
                ScopeInterface::SCOPE_STORE
            );
    }

    public function getMinMaxForCustomSize($_product)
    {
        $sizeLimits = [];
        $pricingLimits = $_product->getPricingLimit();
        $pricingLimits = explode(";", $_product->getPricingLimit());
        foreach ($pricingLimits as $pricingLimit) {
            $limits = explode("=", $pricingLimit);
            foreach ($limits as $key => $limit) {
                if(isset($limits[0]) && !empty($limits[0])){
                    if(isset($limits[1]) && !empty($limits[1])) {
                        $sizeLimits[$limits[0]] = $limits[1];
                    }
                }
            }
        }
        return $sizeLimits;
    }

}
