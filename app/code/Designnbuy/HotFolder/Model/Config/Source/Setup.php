<?php

namespace Designnbuy\HotFolder\Model\Config\Source;

class Setup implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '', 'label' => __('Select Option')],
            ['value' => '0', 'label' => __('Order Wise')],
            ['value' => '1', 'label' => __('Product Wise')],
        ];
    }
}