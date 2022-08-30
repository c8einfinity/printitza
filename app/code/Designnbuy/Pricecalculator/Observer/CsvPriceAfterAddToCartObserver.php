<?php

namespace Designnbuy\Pricecalculator\Observer;

use \Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;

class CsvPriceAfterAddToCartObserver implements ObserverInterface
{
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

    /**
     * @var \Designnbuy\Pricecalculator\Helper\Data
     */
    private $priceCalculatorHelper;
    
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\RequestInterface $request,
        \Designnbuy\Pricecalculator\Helper\Data $priceCalculatorHelper,
        \Magento\Catalog\Model\ProductRepository $productRepository
    ) {
        $this->_storeManager = $storeManager;
        $this->_request = $request;
        $this->priceCalculatorHelper = $priceCalculatorHelper;
        $this->_productRepository = $productRepository;
    }

    public function execute(Observer $observer)
    {
        $eventName = $observer->getEvent()->getName();
        if($eventName == 'checkout_cart_update_items_after'){
           // $cart = $observer->getEvent()->getCart();
            $quote = $observer->getCart()->getQuote();
            $items = $quote->getAllVisibleItems();
            foreach($items as $item)
            {
                $this->getFinalPrice($item);
            }
            return $this;
        } else {
            $item = $observer->getEvent()->getData('quote_item');
            $item = ($item->getParentItem() ? $item->getParentItem() : $item);
            $this->getFinalPrice($item);
            return $this;
        }

    }

    protected function getFinalPrice($item)
    {
        $itemProduct = $item->getProduct();
        $product = $this->_productRepository->getById($itemProduct->getEntityId());

        $qty = $item->getQty();

        $itemOptions = (array) json_decode($item->getOptionByCode('info_buyRequest')->getValue());
        if(isset($itemOptions) && !empty($itemOptions['options']) && (isset($itemOptions['toolType']) && $itemOptions['toolType'] != "producttool") || (!isset($itemOptions['toolType']) && isset($itemOptions) && !empty($itemOptions['options'] ))) {
            $itemOptionsData = (array) $itemOptions['options'];

            $optionCalculation = $this->priceCalculatorHelper->calculateCSVPrice($product, $itemOptionsData, $qty);
                    
            if($optionCalculation){
                $basePrice = $product->getPrice();
                $item->getProduct()->setPrice($optionCalculation);
                $optionCalculationdt = $item->getProduct()->getFinalPrice($qty,$item->getProduct());
                //$item->getProduct()->setPrice($basePrice);
                $product->setPrice($basePrice);
                $price = $product->getFinalPrice($qty,$product);
            } else {
                $price = $item->getProduct()->getFinalPrice($qty,$product);
            }
            if($item->getProduct()->getAbsolutePrice()){
                if($optionCalculation){
                    $price = 0;
                    if((int)$optionCalculation != (int)$optionCalculationdt){
                        $optionCalculation = $optionCalculation + $optionCalculationdt;
                    } else {
                        $optionCalculation = $optionCalculationdt;    
                    }
                }
            } else {
                if($optionCalculation){
                    $optionCalculation = $optionCalculationdt;
                    //echo $price ."+". $optionCalculationdt; exit;
                }
            }
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $priceCurrencyObject = $objectManager->get('Magento\Framework\Pricing\PriceCurrencyInterface'); 
            //instance of PriceCurrencyInterface
            $storeManager = $objectManager->get('Magento\Store\Model\StoreManagerInterface'); 
            //instance of StoreManagerInterface
            $store = null;
            if ($store == null) {
                $store = $storeManager->getStore()->getStoreId(); 
                //get current store id if store id not get passed
            }
            
            if($storeManager->getStore()->getCurrentCurrencyCode() != $storeManager->getStore()->getDefaultCurrencyCode()){
                $price = $priceCurrencyObject->convert($price, $store, null); 
            }
            //echo $price ."+". $optionCalculation; exit;
            $optionPrice = $price + $optionCalculation;

            $item->setCustomPrice($optionPrice);
            $item->setOriginalCustomPrice($optionPrice);
            //if (!$item->getOriginalPrice()) {
                $item->setOriginalPrice($price);
            //}
            $item->getProduct()->setIsSuperMode(true);
        }
    }
}