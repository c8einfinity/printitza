<?php

namespace Designnbuy\Base\Model\Config\Source;

class Watermark implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'image', 'label' => __('Image')],
            ['value' => 'text', 'label' => __('Text')],
        ];
    }
}