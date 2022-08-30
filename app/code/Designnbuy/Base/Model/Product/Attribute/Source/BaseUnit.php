<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 *
 * Catalog product base_unit attribute source
 */
namespace Designnbuy\Base\Model\Product\Attribute\Source;

class BaseUnit extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**#@+
     * Product Base Unit values
     */

    const PLEASE_SELECT = '';
    const UNIT_INCHES = 'in';
    const UNIT_MILIMETER = 'mm';
    const UNIT_CENTIMETER = 'cm';
    const UNIT_PIXELS = 'px';
    const UNIT_METER = 'm';
    const UNIT_FEET = 'ft';

    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public static function getOptionArray()
    {
        return [
            self::PLEASE_SELECT => __('-- Please Select --'),
            self::UNIT_INCHES => __('Inches'),
            self::UNIT_MILIMETER => __('Milimeter'),
            self::UNIT_CENTIMETER => __('Centimeter'),
            self::UNIT_PIXELS => __('Pixels'),
            self::UNIT_METER => __('Meter'),
            self::UNIT_FEET => __('Feet')
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
