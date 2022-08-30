<?php
/**
 * Copyright © 2019 Designnbuy WebToPrint Pvt. Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 * ALWAYS DO BETTER @a
 */

namespace Drc\Storepickup\Model\Config\Source;

/**
 * Used in creating options for commission type config value selection
 */
class HolidayStatus implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @const string
     */
    const ENABLED = 1;

    /**
     * @const string
     */
    const DISABLED = 0;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::ENABLED, 'label' => __('Enabled')],
            ['value' => self::DISABLED, 'label' => __('Disabled')],
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $array = [];
        foreach ($this->toOptionArray() as $item) {
            $array[$item['value']] = $item['label'];
        }
        return $array;
    }
}
