<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */
namespace Designnbuy\Canvas\Model\Config\Source;

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
            ['value' => 'm', 'label' => __('Meter')],
            ['value' => 'ft', 'label' => __('Feet')]
        ];
    }
}
