<?php

namespace Designnbuy\Base\Plugin\Cart;

class Edit
{

    const DESIGN = 'design';
    const TEMPLATE = 'template';

    public function __construct(
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Framework\UrlInterface $url,
        \Designnbuy\Base\Helper\Data $dnbBaseHelper,
        \Designnbuy\Designidea\Model\DesignideaFactory $designideaFactory,
        \Designnbuy\Template\Model\TemplateFactory $templateFactory
    ) {
        $this->cart = $cart;
        $this->_url = $url;
        $this->dnbBaseHelper = $dnbBaseHelper;
        $this->designideaFactory = $designideaFactory;
        $this->templateFactory = $templateFactory;
    }


    public function afterGetConfigureUrl($item, $result)
    {
        $_item = $item->getItem();
        $quoteItem = $this->cart->getQuote()->getItemById($_item->getId());
        if($quoteItem){
            $infoBuyRequest = $quoteItem->getOptionByCode('info_buyRequest');
            $itemOptions = (array) json_decode($quoteItem->getOptionByCode('info_buyRequest')->getValue());

            if(isset($itemOptions['from_quickedit']) && !empty($itemOptions['from_quickedit'])){
                return $this->_url->getUrl(
                    'checkout/cart/configure',
                    [
                        'id' => $_item->getId(),
                        'product_id' => $_item->getProduct()->getId()
                    ]
                );
            }

            if(isset($itemOptions['png']) && !empty($itemOptions['png'])){
                $product = $quoteItem->getProduct();
                if($product->getAttributeSetId() == $this->dnbBaseHelper->getCustomProductAttributeSetId()){
                    return $this->_url->getUrl(
                        'merchandise/index/index',
                        [
                            'item_id' => $_item->getId(),
                            'id' => $_item->getProduct()->getId()
                        ]
                    );
                } elseif($product->getAttributeSetId() == $this->dnbBaseHelper->getCustomCanvasAttributeSetId()){
                    return $result = $this->_url->getUrl(
                        'canvas/index/index',
                        [
                            'item_id' => $_item->getId(),
                            'id' => $_item->getProduct()->getId()
                        ]
                    );
                }
            }
            /*For Designer*/
            if(!isset($itemOptions['svg']) && isset($itemOptions['designer_design_id']) && !empty($itemOptions['designer_design_id'])) {
                $designId = $itemOptions['designer_design_id'];
                if($itemOptions['toolType'] == 'web2print'){
                    $design = $this->templateFactory->create();
                    $design->load($designId);
                    $identifier = $design->getIdentifier();
                    return $this->_url->getUrl(self::TEMPLATE . '/' . $identifier);
                } else {
                    $design = $this->designideaFactory->create();
                    $design->load($designId);
                    $identifier = $design->getIdentifier();
                    return $this->_url->getUrl(self::DESIGN . '/' . $identifier);
                }
            }
            if(isset($itemOptions['photoAlbum']) && $itemOptions['photoAlbum'] == 'true'){
                return 'none';
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