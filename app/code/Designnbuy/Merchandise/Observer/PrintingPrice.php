<?php
/**
 * Created by PhpStorm.
 * User: Ajay
 * Date: 25-02-2016
 * Time: 12:30
 */
namespace Designnbuy\Merchandise\Observer;

use \Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\Filesystem\Io\File;
use Magento\Framework\App\Filesystem\DirectoryList;
use DOMDocument;
use INKSCAPE_INKSCAPE;
class PrintingPrice implements ObserverInterface
{
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
 
    protected $_request;
	
	protected $_directoryList;

    /**
     * @var \Designnbuy\PrintingMethod\Helper\Data
     */
    private $printingMethodHelper;

    /**
     * @var \Designnbuy\Base\Helper\Data
     */
    private $dnbBaseHelper;

	
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\App\RequestInterface $request,
        \Designnbuy\PrintingMethod\Helper\Data $printingMethodHelper,
        \Designnbuy\Base\Helper\Data $dnbBaseHelper
    ) {
        $this->_storeManager = $storeManager;
        $this->_request = $request;
        $this->printingMethodHelper = $printingMethodHelper;
        $this->dnbBaseHelper = $dnbBaseHelper;
    }

    public function execute(Observer $observer)
    {
        $eventName = $observer->getEvent()->getName();
        \Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug('your debug message', array('test'=> $eventName));

        if($eventName == 'checkout_cart_update_items_after'){
            // $cart = $observer->getEvent()->getCart();
            $quote = $observer->getCart()->getQuote();
            $items = $quote->getAllVisibleItems();

            $qtyData = array();
            foreach($items as $item)
            {
                $itemOptions = (array) json_decode($item->getOptionByCode('info_buyRequest')->getValue());
                if(array_key_exists('currentTime',$itemOptions)){
                    $currentTime = $itemOptions['currentTime'];
                    $qtyData[$currentTime][] = $item->getQty();
                }
            }
            foreach($items as $item)
            {
                $this->getFinalPrice($item,$qtyData);
            }
            return $this;
        } else {
            $item = $observer->getEvent()->getData('quote_item');
            $item = ($item->getParentItem() ? $item->getParentItem() : $item);
            $qtyData = array();
            $this->getFinalPrice($item,$qtyData);
            return $this;
        }

    }

    private function getFinalPrice($item,$qtyData)
    {
        $qty = $item->getQty();
        $printingPrice = 0;
        $nameTotalPrice = 0;
        $numberTotalPrice = 0;
        $itemOptions = (array) json_decode($item->getOptionByCode('info_buyRequest')->getValue());
        if(isset($itemOptions) && !empty($itemOptions['printingMethod'])){
            $printingMethodData = json_decode($itemOptions['printingMethod'], true);
            if(array_key_exists('printingMethodId',$printingMethodData) && $printingMethodData['printingMethodId'] != 0){
                if(isset($itemOptions['nameNumber']) && !empty($itemOptions['nameNumber'])){
                    $printingMethodData['nameNumber'] = (array) $itemOptions['nameNumber'];
                }

                if (isset($printingMethodData) && !empty($printingMethodData)) {
                    if(array_key_exists('currentTime',$itemOptions) && !empty($qtyData) && array_key_exists($itemOptions['currentTime'],$qtyData)){
                        $currentTime = $itemOptions['currentTime'];
                        $totalqtys = $qtyData[$currentTime];
                        $qty = array_sum($totalqtys);
                    }elseif(array_key_exists('totalQty',$itemOptions) && $itemOptions['totalQty'] != ''){
                        $qty = $itemOptions['totalQty'];
                    }
                    $printingPrices = $this->printingMethodHelper->calculatePrintingMethodPrice($printingMethodData, $qty);

                    if(isset($printingPrices) && !empty($printingPrices)){
                        $printingPrice = $printingPrices['printingPrice'];
                    }

                    $nameTotalPrice = $printingPrices['nameTotalPrice'];
                    $numberTotalPrice = $printingPrices['numberTotalPrice'];
                    $printingPrice = $printingPrice + $nameTotalPrice + $numberTotalPrice;
                }
            } else {

                if(isset($itemOptions['nameNumber']) && !empty($itemOptions['nameNumber'])){
                    $nameNumber = (array) $itemOptions['nameNumber'];

                    if(isset($nameNumber['totalname']) && !empty($nameNumber['totalname'])){
                        $totalName = $nameNumber['totalname'];
                        $namePrice = (float)$this->dnbBaseHelper->getNamePrice();
                        $nameTotalPrice = $namePrice;
                    }

                    if(isset($nameNumber['totalnumber']) && !empty($nameNumber['totalnumber'])){
                        $totalNumber = $nameNumber['totalnumber'];
                        $numberPrice = (float)$this->dnbBaseHelper->getNumberPrice();
                        $numberTotalPrice = $numberPrice;
                    }

                    $printingPrice = $nameTotalPrice + $numberTotalPrice;
                }
            }
            $price = $item->getProduct()->getFinalPrice($qty,$item->getProduct());
            $finalPrice = $price + $printingPrice;

            $item->setCustomPrice($finalPrice);
            $item->setOriginalCustomPrice($finalPrice);
            //if (!$item->getOriginalPrice()) {
            $item->setOriginalPrice($price);
            //}
            // Enable super mode on the product.
            $item->getProduct()->setIsSuperMode(true);

        }
    }

}