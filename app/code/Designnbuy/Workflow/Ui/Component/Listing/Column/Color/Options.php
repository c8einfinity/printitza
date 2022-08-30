<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Workflow\Ui\Component\Listing\Column\Color;

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
            ['value' => 'red_class', 'label' => __('Red')],
            ['value' => 'blue_class', 'label' => __('Blue')],
            ['value' => 'green_class', 'label' => __('Green')],
            ['value' => 'yellow_class', 'label' => __('Yellow')],
            ['value' => 'black_class', 'label' => __('Black')],
            ['value' => 'orange_class', 'label' => __('Orange')],
        ];


        $this->options = array_values($this->currentOptions);

        return $this->options;
    }
}
