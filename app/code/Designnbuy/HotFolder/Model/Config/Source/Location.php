<?php

namespace Designnbuy\HotFolder\Model\Config\Source;

class Location implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '', 'label' => __('Select Option')],
            ['value' => '0', 'label' => __('Same Server')],
            ['value' => '1', 'label' => __('Remote Server')],
        ];
    }
}