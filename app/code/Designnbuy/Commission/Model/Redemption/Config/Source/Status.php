<?php

namespace Designnbuy\Commission\Model\Redemption\Config\Source;

/**
 * Used in creating options for commission type config value selection
 */
class Status implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @const string
     */
    const REDEMPTION_PAID = 1;

    /**
     * @const string
     */
    const REDEMPTION_PENDING = 0;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::REDEMPTION_PENDING, 'label' => __('Pending')],
            ['value' => self::REDEMPTION_PAID, 'label' => __('Paid')],
            
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
