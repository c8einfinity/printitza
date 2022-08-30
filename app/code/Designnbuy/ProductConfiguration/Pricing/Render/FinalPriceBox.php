<?php

namespace Designnbuy\ProductConfiguration\Pricing\Render;

class FinalPriceBox
{
    function aroundToHtml($subject, callable $proceed)
    {
        if($subject->getSaleableItem()->getHidePrice() == 1){
            return '';
        }
    
        return $proceed();
    }

}