<?php

namespace Designnbuy\Merchandise\Model\Config\Source;

class Order implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'csv', 'label' => __('CSV')],
            ['value' => 'xml', 'label' => __('XML')],
            ['value' => 'txt', 'label' => __('TXT')],
        ];
    }
}