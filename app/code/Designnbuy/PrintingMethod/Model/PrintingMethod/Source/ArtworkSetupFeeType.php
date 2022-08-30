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

class ArtworkSetupFeeType implements \Magento\Framework\Data\OptionSourceInterface
{
    /**#@+
     * Product Base Unit values
     */
    const FIXED = 1;
    const PER_COLOR = 2;

    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public static function getOptionArray()
    {
        return [
            self::FIXED => __('Fixed'),
            self::PER_COLOR => __('Per Color')
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
