<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Vendor\Ui\Component\Listing\Column\Color;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Store Options for Cms Pages and Blocks
 */
class Options implements OptionSourceInterface
{
    /**
     * @var array
     */
    protected $options;

    /**
     * @var array
     */
    protected $currentOptions = [];

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options !== null) {
            return $this->options;
        }
        $this->currentOptions = [
            ['value' => 'red', 'label' => __('Red')],
            ['value' => 'blue', 'label' => __('Blue')],
            ['value' => 'Green', 'label' => __('Green')],
            ['value' => 'yellow', 'label' => __('Yellow')],
        ];


        $this->options = array_values($this->currentOptions);

        return $this->options;
    }
}
