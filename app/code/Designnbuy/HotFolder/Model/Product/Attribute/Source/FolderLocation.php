<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 *
 * Catalog product base_unit attribute source
 */
namespace Designnbuy\HotFolder\Model\Product\Attribute\Source;

class FolderLocation extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**#@+
     * Product Base Unit values
     */
    const GLOBAL_SETIINGS = 1;
    const VENDOR_LOCATION = 2;

    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public static function getOptionArray()
    {
        return [
            self::GLOBAL_SETIINGS => __('Global'),
            self::VENDOR_LOCATION => __('Vendor Location')
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
