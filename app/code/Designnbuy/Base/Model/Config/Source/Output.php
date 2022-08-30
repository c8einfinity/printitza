<?php

namespace Designnbuy\Base\Model\Config\Source;

class Output implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'rgb', 'label' => __('RGB')],
            ['value' => 'rgbcmyk', 'label' => __('RGB + CMYK')],
            /*['value' => 'spot', 'label' => __('Spot Color')],*/
        ];
    }
}