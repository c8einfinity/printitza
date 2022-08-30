<?php

namespace Designnbuy\Canvas\Model\Config\Source;

class Output implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'svg', 'label' => __('SVG')],
            ['value' => 'pdf', 'label' => __('PDF')],
            ['value' => 'both', 'label' => __('BOTH')],
        ];
    }
}