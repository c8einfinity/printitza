<?php

namespace Designnbuy\Canvas\Model\Config\Source;

class Vdp implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Automatic')],
            ['value' => 1, 'label' => __('Manual')],
        ];
    }
}