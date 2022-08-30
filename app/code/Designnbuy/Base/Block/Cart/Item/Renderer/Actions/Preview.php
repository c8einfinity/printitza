<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Base\Block\Cart\Item\Renderer\Actions;
use Magento\Checkout\Block\Cart\Item\Renderer\Actions\Generic;
use Magento\Backend\Block\Template\Context;
class Preview extends Generic
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

    /**
     * Retrieve product images in JSON format
     *
     * @return string
     */
    public function getImagesJson()
    {
        $pngs = [];
        $imagesItems = [];
        $pngs = $this->getImages();
        if(isset($pngs) && !empty($pngs)){
            $i = 0;
            foreach ($pngs as $png) {
                if(file_exists($this->_outputHelper->getCartDesignsDir().$png)){
                    $imagesItems[] = [
                        'thumb' => $this->_outputHelper->getCartDesignsUrl().$png,
                        'img' => $this->_outputHelper->getCartDesignsUrl().$png,
                        'full' => $this->_outputHelper->getCartDesignsUrl().$png,
                        'position' => $i
                    ];
                    $i++;
                }
            }

        }

        return json_encode($imagesItems);
    }

    public function getImages()
    {
        $_item = $this->getItem();
        $pngs = [];
        $quoteItem = $this->cart->getQuote()->getItemById($_item->getId());
        if($quoteItem){
            $infoBuyRequest = $quoteItem->getOptionByCode('info_buyRequest');
            $itemOptions = (array) json_decode($quoteItem->getOptionByCode('info_buyRequest')->getValue());
            if(isset($itemOptions['png']) && !empty($itemOptions['png'])){
                $pngs = [];
                $pngs = explode(',', $itemOptions['png']);
            }
        }
        return $pngs;
    }

    /**
     * Return magnifier options
     *
     * @return string
     */
    public function getMagnifier()
    {
        return json_encode($this->getVar('magnifier'));
    }

    /**
     * Return breakpoints options
     *
     * @return string
     */
    public function getBreakpoints()
    {
        return json_encode($this->getVar('breakpoints'));
    }
    /**
     * @param string $imageId
     * @param string $attributeName
     * @param string $default
     * @return string
     */
    public function getImageAttribute($imageId, $attributeName, $default = null)
    {
        $attributes =
            $this->getConfigView()->getMediaAttributes('Magento_Catalog', Image::MEDIA_TYPE_CONFIG_NODE, $imageId);
        return isset($attributes[$attributeName]) ? $attributes[$attributeName] : $default;
    }
}
