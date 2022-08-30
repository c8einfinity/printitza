<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace BrainActs\SalesRepresentative\Model\Source\Rate;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class User
 */
class Type implements OptionSourceInterface
{

    /**
     * @var array
     */
    private $options;

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

        $options = [
            [
                'label' => __('Percent'),
                'value' => 1,
            ],
            [
                'label' => __('Fixed'),
                'value' => 2,
            ],

        ];

        $this->options = $options;

        return $this->options;
    }
}
