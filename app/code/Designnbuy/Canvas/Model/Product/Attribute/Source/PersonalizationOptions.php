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

class PersonalizationOptions extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**#@+
     * Product Base Unit values
     */
    const PLEASE_SELECT = '';
    const CUSTOMIZE_ONLY = 1;
    const CUSTOMIZE_QUICK_EDIT = 2;
    const QUICK_EDIT = 3;
    const MULTIPLE_TEMPLATE = 4;

    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public static function getOptionArray()
    {
        return [
            self::PLEASE_SELECT => __('-- Please Select --'),
            self::CUSTOMIZE_ONLY => __('Customize Only'),
            self::CUSTOMIZE_QUICK_EDIT => __('Customize with Quick Edit'),
            self::QUICK_EDIT => __('Quick Edit Only'),
            self::MULTIPLE_TEMPLATE => __('Multiple Design Templates')
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
