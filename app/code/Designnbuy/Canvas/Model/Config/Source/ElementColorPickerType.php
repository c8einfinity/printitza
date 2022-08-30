<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 *
 * Catalog product base_unit attribute source
 */
namespace Designnbuy\Canvas\Model\Config\Source;

class ElementColorPickerType implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => '1', 'label' => __('Full Color Picker')],
            ['value' => '2', 'label' => __('Color Palette (Choose colors)')],
        ];
    }
}
