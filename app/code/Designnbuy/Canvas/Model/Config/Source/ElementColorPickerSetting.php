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

class ElementColorPickerSetting extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            1 => __('4-color Process'),
            2 => __('1-color Process'),
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

        foreach (self::toOptionArray() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }

        return $result;
    }

    public function getOptionText($optionId)
    {
        $options = self::toOptionArray();

        return isset($options[$optionId]) ? $options[$optionId] : null;
    }
}
