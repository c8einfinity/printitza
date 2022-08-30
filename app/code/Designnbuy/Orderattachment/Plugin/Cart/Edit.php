<?php

namespace Designnbuy\Orderattachment\Plugin\Cart;

class Edit
{
    public function __construct(
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Framework\UrlInterface $url,
        \Designnbuy\Orderattachment\Helper\Data $helper
    ) {
        $this->cart = $cart;
        $this->_url = $url;
        $this->helper = $helper;
    }

    public function afterGetConfigureUrl($item, $result) 
    {
        $_item = $item->getItem();
        $quoteItem = $this->cart->getQuote()->getItemById($_item->getId());
        //print_r($_item);die('8888');
        if($quoteItem) {
            $infoBuyRequest = $quoteItem->getOptionByCode('attachment');
            $itemOptions = (array) json_decode($quoteItem->getOptionByCode('fileName')->getValue());
            if(isset($itemOptions['fileName']) && !empty($itemOptions['fileName'])) {
                return $this->_url->getUrl(
                    'checkout/cart/configure',
                    [
                        'id' => $_item->getId(),
                        'product_id' => $_item->getProduct()->getId()
                    ]
                );
            }

            if(isset($itemOptions['png']) && !empty($itemOptions['png'])) {
                $product = $quoteItem->getProduct();
                if($product->getAttributeSetId() == $this->helper->getCustomProductAttributeSetId()){
                    return $editUrl = $this->_url->getUrl(
                        'merchandise/index/index',
                        [
                            'item_id' => $_item->getId(),
                            'id' => $_item->getProduct()->getId()
                        ]
                    );
                } elseif($product->getAttributeSetId() == $this->helper->getCustomCanvasAttributeSetId()){
                    return $editUrl = $this->_url->getUrl(
                        'canvas/index/index',
                        [
                            'item_id' => $_item->getId(),
                            'id' => $_item->getProduct()->getId()
                        ]
                    );
                }
            }
        }

        return $this->_url->getUrl(
            'checkout/cart/configure',
            [
                'id' => $_item->getId(),
                'product_id' => $_item->getProduct()->getId()
            ]
        );

    }
}