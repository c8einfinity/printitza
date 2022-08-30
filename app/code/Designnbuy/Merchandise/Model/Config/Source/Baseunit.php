<?php

namespace Designnbuy\Merchandise\Model\Config\Source;

class Baseunit implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'in', 'label' => __('Inches')],
            ['value' => 'mm', 'label' => __('Milimeter')],
            ['value' => 'cm', 'label' => __('Centimeter')],
            ['value' => 'px', 'label' => __('Pixels')],
        ];
    }
}
