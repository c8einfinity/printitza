<?php

namespace Designnbuy\Pricecalculator\Block;

/**
 * Class Attachment
 * @package Designnbuy\Productattach\Block
 */
class Pricecalculate extends \Magento\Framework\View\Element\Template
{

    protected $scopeConfig;

    protected $_helper;

    protected $_coreRegistry = null;

    protected $session;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Directory\Model\Currency $localeCurrency,
        \Designnbuy\Pricecalculator\Helper\Data $helper,
        \Magento\Store\Model\StoreManagerInterface $storeInterface,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_helper = $helper;
        $this->localeCurrency = $localeCurrency;
        $this->storeInterface = $storeInterface;
        $this->session = $customerSession;
        parent::__construct($context, $data);
    }

    /**
     * @return $this
     */

    public function getCurrentProduct() {
        return $this->_coreRegistry->registry('current_product');
    }

    public function getPriceCalculation(){
        return "price calculation";
    }

    public function getPriceWithSymbol() {
        $store = $this->storeInterface->getStore();
        $currencyCode = $store->getCurrentCurrencyCode();
        $currencySymbol = $this->localeCurrency->load($currencyCode)->getCurrencySymbol();
        $precision = 2;
        $price = $this->getCurrentProduct()->getFinalPrice();
        $formattedPrice = $this->localeCurrency->format($price, ['symbol' => $currencySymbol, 'precision'=> $precision], false, false);
        return $formattedPrice;
        
    }

    public function getStoreCurrencySymbol()
    {
        return $this->localeCurrency->getCurrencySymbol();
    }

    public function showCsvCalculation() {
        $_product = $this->getCurrentProduct();

        $enabled = false;
        if($_product->getEnableCustomHeightWidth() == 1){
            $enabled = true;
        }
        return $enabled;
    }

    public function getSquareAreaPrices()
    {
        $_product = $this->getCurrentProduct();
        $squareAreaPrices = [];
        if($_product->getEnableCustomHeightWidth() == 1){
            $productSquareAreaPrices = $_product->getSquareAreaPrice();
            foreach ($productSquareAreaPrices as $productSquareAreaPrice) {
                if ($this->session->isLoggedIn()) {
                    if($this->session->getCustomer()->getGroupId() && $this->session->getCustomer()->getGroupId() == $productSquareAreaPrice['cust_group'] || $productSquareAreaPrice['cust_group'] == '32000'){
                        $squareAreaPrices[$productSquareAreaPrice['price_qty']] = $productSquareAreaPrice['price'];
                    }
                } else {
                    if($productSquareAreaPrice['cust_group'] == '32000' || $productSquareAreaPrice['cust_group'] == '0'){
                        $squareAreaPrices[$productSquareAreaPrice['price_qty']] = $productSquareAreaPrice['price'];
                    }
                }
                
            }
        }

        return json_encode($squareAreaPrices);
    }

    public function getSquareAreaPrice()
    {
        $_product = $this->getCurrentProduct();
        print_r($this->_helper->getSquareAreaPrice(100, $_product));
        die;
    }


    public function getMinMaxSize()
    {
        $_product = $this->getCurrentProduct();
        return $this->_helper->getMinMaxForCustomSize($_product);
    }
    public function getConfigProductUnit()
    {
        if($this->_helper->getConfigProductUnit() == 'in') {
            return 'Inches';
        } else if($this->_helper->getConfigProductUnit() == 'mm') {
            return 'Milimeter';
        } else if($this->_helper->getConfigProductUnit() == 'cm') {
            return 'Centimeter';
        } else if($this->_helper->getConfigProductUnit() == 'px') {
            return 'Pixels';
        } else {
            return "Square Area";
        }
        return "Square Area";
    }
}
