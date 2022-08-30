<?php

namespace Designnbuy\Base\Plugin\Cart;

class Wishlist
{
    public function __construct(
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Wishlist\Helper\Data $wishlistHelper,
        \Magento\Framework\UrlInterface $url,
        \Designnbuy\Base\Helper\Data $dnbBaseHelper
    ) {
        $this->cart = $cart;
        $this->wishlistHelper = $wishlistHelper;
        $this->_url = $url;
        $this->dnbBaseHelper = $dnbBaseHelper;
    }


    public function afterIsAllowInCart($item, $result)
    {

        $_item = $item->getItem();
        $quoteItem = $this->cart->getQuote()->getItemById($_item->getId());
        if($quoteItem){
            $infoBuyRequest = $quoteItem->getOptionByCode('info_buyRequest');
            $itemOptions = (array) json_decode($quoteItem->getOptionByCode('info_buyRequest')->getValue());
            if(isset($itemOptions['png']) && !empty($itemOptions['png'])){
                return false;
            }
        }
        return $this->wishlistHelper->isAllowInCart();

    }
}