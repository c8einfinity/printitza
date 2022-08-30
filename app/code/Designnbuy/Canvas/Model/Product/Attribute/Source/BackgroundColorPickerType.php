<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 *
 * Catalog product base_unit attribute source
 */
namespace Designnbuy\Canvas\Model\Product\Attribute\Source;

class BackgroundColorPickerType extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**#@+
     * Product Base Unit values
     */
    const PLEASE_SELECT = '';
    const BG_FULL_COLOR = 1;
    const BG_CUSTOM_COLOR = 2;
    const BG_PRINTABLE_COLOR = 3;

    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public static function getOptionArray()
    {
        return [
            self::PLEASE_SELECT => __('Please Select'),
            self::BG_FULL_COLOR => __('Full Color Picker'),
            self::BG_PRINTABLE_COLOR => __('Color Palette (Choose colors)'),
            self::BG_CUSTOM_COLOR => __('Custom Options (RGB)')
        ];
    }

    /**
     * Retrieve option array with empty value
     *
     * @return string[]
     */
    public function getAllOptions()
    {
        $result = [];

        foreach (self::getOptionArray() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }

        return $result;
    }

    /**
     * Retrieve option text by option value
     *
     * @param string $optionId
     * @return string
     */
    public function getOptionText($optionId)
    {
        $options = self::getOptionArray();

        return isset($options[$optionId]) ? $options[$optionId] : null;
    }
}
