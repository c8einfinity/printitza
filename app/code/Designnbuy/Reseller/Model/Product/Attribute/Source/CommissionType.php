<?php

namespace Designnbuy\Reseller\Model\Product\Attribute\Source;

class CommissionType extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * @const string
     */
    const COMMISSION_PERCENTAGE = 1;

    /**
     * @const string
     */
    const COMMISSION_FIXED = 2;

    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public static function getOptionArray()
    {
        return [
            self::COMMISSION_PERCENTAGE => __('Percentage'),
            self::COMMISSION_FIXED => __('Fixed')
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
