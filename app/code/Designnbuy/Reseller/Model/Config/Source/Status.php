<?php
/**
 * Created by PhpStorm.
 * User: Ashok
 * Date: 08-Jun-17
 * Time: 2:25 PM
 */

namespace Designnbuy\Reseller\Model\Config\Source;

class Status implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        $options = [
            [
                'label' => __('Pending'),
                'value' => null,
            ],
            [
                'label' => __('Declined'),
                'value' => '0',
            ],
            [
                'label' => __('Approved'),
                'value' => '1',
            ],
        ];
        return $options;
    }
}