<?php
/**
 * Created by PhpStorm.
 * User: Ashok
 * Date: 08-Jun-17
 * Time: 2:25 PM
 */

namespace Designnbuy\Reseller\Model\Config\Source;

class PoolStatus implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        $options = [
            [
                'label' => __('Enable'),
                'value' => '1',
            ],
            [
                'label' => __('Disable'),
                'value' => '0',
            ],
        ];
        return $options;
    }
}