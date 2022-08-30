<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\CustomerPhotoAlbum\Block;
use Magento\Checkout\Block\Cart\Item\Renderer\Actions\Generic;
use Magento\Backend\Block\Template\Context;
class CartAlbum extends Generic
{

    public function __construct(
        Context $context,
        \Magento\Checkout\Model\Cart $cart,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->cart = $cart;
    }

    public function getItemId()
    {
        return $this->getItem()->getId();
    }

    public function getPhotoAlbum()
    {
        $_item = $this->getItem();
        $photoAlbum = false;
        $quoteItem = $this->cart->getQuote()->getItemById($_item->getId());
        if($quoteItem){
            $infoBuyRequest = $quoteItem->getOptionByCode('info_buyRequest');
            $itemOptions = (array) json_decode($quoteItem->getOptionByCode('info_buyRequest')->getValue(), true);
            if(isset($itemOptions['photoAlbum']) && $itemOptions['photoAlbum'] == 'true'){
                $photoAlbum = true;
            }
        }
        return $photoAlbum;
    }

}
