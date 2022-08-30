<?php

namespace Designnbuy\Base\Plugin\Cart;

class Image
{
    public function __construct(
        \Magento\Checkout\Model\Cart $cart,
        \Designnbuy\Base\Helper\Output $outputHelper
    ) {
        $this->cart = $cart;
        $this->_outputHelper = $outputHelper;
    }


    public function afterGetImage($item, $result)
    {
        $_item = $item->getItem();
        $quoteItem = $this->cart->getQuote()->getItemById($_item->getId());
        if($quoteItem){
            $infoBuyRequest = $quoteItem->getOptionByCode('info_buyRequest');

            $itemOptions = (array) json_decode($quoteItem->getOptionByCode('info_buyRequest')->getValue());

            if(isset($itemOptions['png']) && !empty($itemOptions['png'])){
                $pngs = explode(',', $itemOptions['png']);
                if(isset($pngs[0]) && !empty($pngs[0])){
                    if(file_exists($this->_outputHelper->getCartDesignsDir().$pngs[0])){
                        $result->setImageUrl( $this->_outputHelper->getCartDesignsUrl().$pngs[0] );
                        $result->setHeight(100);
                    }
                }
            } 
        }

        return $result;
    }
}