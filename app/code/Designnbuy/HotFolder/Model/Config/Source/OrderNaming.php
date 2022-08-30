<?php

namespace Designnbuy\HotFolder\Model\Config\Source;

class OrderNaming implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
           // ['value' => '', 'label' => __('Select Option')],
            ['value' => '0', 'label' => __('Order ID & Item ID')],
        ];
    }
}