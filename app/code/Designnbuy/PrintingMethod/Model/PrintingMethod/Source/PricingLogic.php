<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 *
 * Catalog product base_unit attribute source
 */
namespace Designnbuy\PrintingMethod\Model\PrintingMethod\Source;

class PricingLogic implements \Magento\Framework\Data\OptionSourceInterface
{
    /**#@+
     * Product Base Unit values
     */
    const FIXED = 1;
    const NO_OF_COLORS = 2;
    const SQUARE_AREA = 3;

    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public static function getOptionArray()
    {
        return [
            self::FIXED => __('Fixed'),
            self::NO_OF_COLORS => __('No. of Colors'),
            self::SQUARE_AREA => __('Square Area')
        ];
    }

    /**
     * Retrieve option array with empty value
     *
     * @return string[]
     */
    public function toOptionArray()
    {
        $result = [];

        foreach (self::getOptionArray() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }

        return $result;
    }

}
