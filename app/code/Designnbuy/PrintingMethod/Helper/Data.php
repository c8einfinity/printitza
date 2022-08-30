<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\PrintingMethod\Helper;


/**
 * Designnbuy PrintingMethod Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Designnbuy\PrintingMethod\Model\PrintingMethodFactory $printingmethodFactory
    ) {
        $this->printingmethodFactory = $printingmethodFactory;
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * Retrieve translated & formated date
     * @param  string $format
     * @param  string $dateOrTime
     * @return string
     */
    public static function getTranslatedDate($format, $dateOrTime)
    {
    	$time = is_numeric($dateOrTime) ? $dateOrTime : strtotime($dateOrTime);
        $month = ['F' => '%1', 'M' => '%2'];

        foreach ($month as $from => $to) {
            $format = str_replace($from, $to, $format);
        }

        $date = date($format, $time);

        foreach ($month as $to => $from) {
            $date = str_replace($from, __(date($to, $time)), $date);
        }

        return $date;
    }

    public function calculatePrintingMethodPrice($pricingData, $qty = 1){
        $printingPrice = 0;
        $frontFixedPrice = 0;
        $backFixedPrice = 0;
        $leftFixedPrice = 0;
        $rightFixedPrice = 0;
        $totalFixedPrice = 0;

        $frontQCPrice = 0;
        $backColors = 0;
        $leftQCPrice = 0;
        $rightQCPrice = 0;
        $totalQcPrice = 0;

        $frontQAPrice = 0;
        $backQAPrice = 0;
        $leftQAPrice = 0;
        $rightQAPrice = 0;
        $totalSquareAreaPrice = 0;
        
        $imageTotalPrice = 0;
        $artWorkSetupPrice = 0;

        $frontQCPrice = 0;
        $backQCPrice = 0;
        $leftQCPrice = 0;
        $rightQCPrice = 0;
        $totalQcPrice = 0;

        /*Name Number Price*/
        $nameTotalPrice = 0;
        $numberTotalPrice = 0;

        if(isset($pricingData) && !empty($pricingData['printingMethodId'])) {
            $pritingMetohdData = $pricingData;
            $printingMethodId = $pricingData['printingMethodId'];
            $printingMethod = $this->printingmethodFactory->create();
            $printingMethod->load($printingMethodId);

            if ($printingMethod->getId()) {
                $pricingLogic = $printingMethod->getPricingLogic();
                /*
                $pricingLogic = 1 = Fixed Price;
                $pricingLogic = 2 = Quantity Color Price;
                $pricingLogic = 3 = Quantity Area Price
                */
                //$qty = 10;

                if ($pricingLogic == 1) {
                    if (isset($pritingMetohdData) && !empty($pritingMetohdData['isCustomized'])) {
                        $isCustomizedSides = $pritingMetohdData['isCustomized'];
                        foreach ($isCustomizedSides as $side => $isCustomizedSide) {
                            if ($isCustomizedSide == true) {
                                $totalFixedPrice += $printingMethod->getPrintingMethodQPrice($qty, $side);
                            }
                        }
                    }

                } else if ($pricingLogic == 2) {
                    if (isset($pritingMetohdData) && !empty($pritingMetohdData['totalColors'])) {
                        $totalColorsSides = $pritingMetohdData['totalColors'];
                        foreach ($totalColorsSides as $side => $totalColorsSide) {
                            if ($totalColorsSide > 0) {
                                $totalQcPrice += $printingMethod->getPrintingMethodQCPrice($totalColorsSide, $qty, $side);
                            }
                        }
                    }

                } else if ($pricingLogic == 3) {
                    if (isset($pritingMetohdData) && !empty($pritingMetohdData['squareArea'])) {
                        $squareAreaSides = $pritingMetohdData['squareArea'];
                        foreach ($squareAreaSides as $side => $squareAreaSide) {
                            if ($squareAreaSide > 0) {
                                $totalQcPrice += $printingMethod->getPrintingMethodQAPrice($squareAreaSide, $qty, $side);
                            }
                        }
                    }

                    $printingPrice = $frontQAPrice + $backQAPrice + $leftQAPrice + $rightQAPrice;
                    $totalSquareAreaPrice = $totalSquareAreaPrice + ($frontQAPrice + $backQAPrice + $leftQAPrice + $rightQAPrice) * $qty;
                }
                $customizedCount = 0;
                $totalNumberOfColors = 0;
                $artWorkSetupPrice = 0;
                $imageTotalPrice = 0;
                $usedImageCounter = 0;
                if (isset($pritingMetohdData) && !empty($pritingMetohdData['isCustomized'])) {
                    $isCustomizedSides = $pritingMetohdData['isCustomized'];
                    foreach ($isCustomizedSides as $isCustomizedSide) {
                        if ($isCustomizedSide == true) {
                            $customizedCount++;
                        }
                    }
                }

                if (isset($pritingMetohdData) && !empty($pritingMetohdData['totalColors'])) {
                    $totalColorsSides = $pritingMetohdData['totalColors'];

                    foreach ($totalColorsSides as $totalColorsSide) {
                        if ($totalColorsSide != '' && $totalColorsSide > 0 ) {
                            $totalNumberOfColors = $totalNumberOfColors + $totalColorsSide;
                        }
                    }
                }

                if ($printingMethod->getArtworkSetupPriceType() == 1) {
                    $artWorkSetupPrice = $printingMethod->getArtworkSetupPrice() * $customizedCount;
                } else {
                    $artWorkSetupPrice = intval($totalNumberOfColors) * $printingMethod->getArtworkSetupPrice();
                }

                if (isset($pritingMetohdData) && !empty($pritingMetohdData['totalImagesUsed'])) {
                    $totalImagesUsedSides = $pritingMetohdData['totalImagesUsed'];
                    foreach ($totalImagesUsedSides as $totalImagesUsedSide) {
                        if ($totalImagesUsedSide > 0) {
                            $usedImageCounter = $usedImageCounter + $totalImagesUsedSide;
                        }
                    }
                }

                /*Image Price*/
                $imageFixPrice = (float)$printingMethod->getImagePrice();
                //$usedImageCounter = $pritingMetohdData['totalImagesUsed']['Front'] + $pritingMetohdData['totalImagesUsed']['Back'] + $pritingMetohdData['totalImagesUsed']['Left'] + $pritingMetohdData['totalImagesUsed']['Right'];
                $printingPrice = $printingPrice + ($usedImageCounter * $imageFixPrice);
                $imageTotalPrice = $imageTotalPrice + ($usedImageCounter * $imageFixPrice * $qty);


                if(isset($pritingMetohdData['nameNumber']) && !empty($pritingMetohdData['nameNumber'])){

                    $nameNumber = (array) $pritingMetohdData['nameNumber'];
                    $totalName = 0;
                    if(isset($nameNumber['totalname']) && !empty($nameNumber['totalname'])){
                        $totalName = $nameNumber['totalname'];
                        $namePrice = (float)$printingMethod->getNamePrice();
                        //$nameTotalPrice = $totalName * $namePrice;
                        $nameTotalPrice = $namePrice;
                    }
                    $totalNumber = 0;
                    if(isset($nameNumber['totalnumber']) && !empty($nameNumber['totalnumber'])){
                        $totalNumber = $nameNumber['totalnumber'];
                        $numberPrice = (float)$printingMethod->getNumberPrice();
                        //$numberTotalPrice = $totalNumber * $numberPrice;
                        $numberTotalPrice = $numberPrice;
                    }
                }
            }
        }
        $printingMethodPrice = array();
        $printingMethodPrice['printingPrice'] = $totalQcPrice + $totalSquareAreaPrice + $totalFixedPrice + $imageTotalPrice;
        $printingMethodPrice['artWorkSetupPrice'] = $artWorkSetupPrice;
        $printingMethodPrice['imageTotalPrice'] = $imageTotalPrice;
        $printingMethodPrice['nameTotalPrice'] = $nameTotalPrice;
        $printingMethodPrice['numberTotalPrice'] = $numberTotalPrice;

        return $printingMethodPrice;
    }

    protected function getQCPrices($printingMethod)
    {
        
    }
}
