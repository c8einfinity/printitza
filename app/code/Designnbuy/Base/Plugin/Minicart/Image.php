<?php

namespace Designnbuy\Base\Plugin\Minicart;

class Image
{
    public function __construct(
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Framework\UrlInterface $url,
        \Designnbuy\Base\Helper\Data $dnbBaseHelper,
        \Designnbuy\Base\Helper\Output $outputHelper
    ) {
        $this->cart = $cart;
        $this->_url = $url;
        $this->dnbBaseHelper = $dnbBaseHelper;
        $this->_outputHelper = $outputHelper;
    }

    public function aroundGetItemData($subject, $proceed, $item)
    {
        $result = $proceed($item);

        $quoteItem = $this->cart->getQuote()->getItemById($item->getId());

        if($quoteItem){
            $itemOptions = (array) json_decode($quoteItem->getOptionByCode('info_buyRequest')->getValue());
            if(isset($itemOptions['png']) && !empty($itemOptions['png'])){
                $pngs = explode(',', $itemOptions['png']);
                if(isset($pngs[0]) && !empty($pngs[0])){
                    if(file_exists($this->_outputHelper->getCartDesignsDir().$pngs[0])){
                        $result['product_image']['src'] = $this->_outputHelper->getCartDesignsUrl().$pngs[0];
                    }
                }
            }

            if(isset($itemOptions['from_quickedit']) && !empty($itemOptions['from_quickedit'])){
                $result['configure_url'] = $this->_url->getUrl(
                    'checkout/cart/configure',
                    [
                        'id' => $item->getId(),
                        'product_id' => $item->getProduct()->getId()
                    ]
                );
            }

            if(isset($itemOptions['png']) && !empty($itemOptions['png'])){
                $product = $quoteItem->getProduct();
                if($product->getAttributeSetId() == $this->dnbBaseHelper->getCustomProductAttributeSetId()){
                    $result['configure_url'] = $this->_url->getUrl(
                        'merchandise/index/index',
                        [
                            'item_id' => $item->getId(),
                            'id' => $item->getProduct()->getId()
                        ]
                    );
                } elseif($product->getAttributeSetId() == $this->dnbBaseHelper->getCustomCanvasAttributeSetId()){
                    $result['configure_url'] = $this->_url->getUrl(
                        'canvas/index/index',
                        [
                            'item_id' => $item->getId(),
                            'id' => $item->getProduct()->getId()
                        ]
                    );
                }
            }
            $_product = $quoteItem->getProduct();
            $infoBuyRequest = $quoteItem->getOptionByCode('info_buyRequest');
            $buyRequest = json_decode($infoBuyRequest->getValue(),true);
            $iscustomQty = false;
            if (is_array($_product->getOptions()) || is_object($_product->getOptions()))
            {
                foreach($_product->getOptions() as $_option) {
                    if($_option->getDesigntoolType() == "quantity")	{
                        $iscustomQty = true;
                    }
                }
            }
            if (array_key_exists("nameNumber",$buyRequest))
            {
                if(isset($buyRequest['nameNumber']['totalname']) && isset($buyRequest['nameNumber']['totalnumber'])){
                    if($buyRequest['nameNumber']['totalname'] != 0 && $buyRequest['nameNumber']['totalnumber'] != 0){
                        $iscustomQty = true;
                    }
                }
            }
            if (array_key_exists("vdp_file",$buyRequest))
            {
                $iscustomQty = true;
            }
            if(isset($buyRequest['photoAlbum']) && $buyRequest['photoAlbum'] == 'true'){
                $iscustomQty = true;
            }
            $result['is_custom_qty'] = $iscustomQty;
        }
        return $result;
    }
}