<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Merchandise\Block\Cart\Item\Renderer\Actions;
use Magento\Checkout\Block\Cart\Item\Renderer\Actions\Generic;
use Magento\Backend\Block\Template\Context;
class NameNumber extends Generic
{

    public function __construct(
        Context $context,
        \Magento\Checkout\Model\Cart $cart,
        \Designnbuy\Base\Helper\Output $outputHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->cart = $cart;
        $this->_outputHelper = $outputHelper;
    }

    public function getItemId()
    {
        return $this->getItem()->getId();
    }

    public function getNameNumber()
    {
        $_item = $this->getItem();
        $nameNumberArray = [];
        $quoteItem = $this->cart->getQuote()->getItemById($_item->getId());
        if($quoteItem){
            $infoBuyRequest = $quoteItem->getOptionByCode('info_buyRequest');
            $itemOptions = (array) json_decode($quoteItem->getOptionByCode('info_buyRequest')->getValue(), true);
            if(isset($itemOptions['nameNumber']) && !empty($itemOptions['nameNumber'])){
                $nameNumber = (array)$itemOptions['nameNumber'];
                if (isset($nameNumber['nameNumberOnly']) && !empty($nameNumber['nameNumberOnly'])) {
                    $totalName = $nameNumber['totalname'];
                    $totalNumber = $nameNumber['totalnumber'];
                    $nameNumberData = $nameNumber['nameNumberOnly'];
                    if ($totalName > 0 || $totalNumber > 0) {
                        if (!empty($nameNumberData)) {
                            foreach ($nameNumberData as $n) {
                                if (isset($itemOptions['super_attribute']) && !empty($itemOptions['super_attribute'])) {
                                    if(in_array($n['id'], $itemOptions['super_attribute'])){
                                        $nameNumberArray[] = $n;
                                    }
                                } else {
                                    $nameNumberArray[] = $n;
                                }
                            }
                        }
                    }
                }
            }
        }
        return $nameNumberArray;
    }

}
