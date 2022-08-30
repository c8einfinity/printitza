<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Workflow\Ui\Component\Listing\Column\Access;

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
            ['value' => 'view_customer_details', 'label' => __('View Customer Details')],
            ['value' => 'view_payment_details', 'label' => __('View Payment Details')],
            ['value' => 'download_files', 'label' => __('Download Files')],
        ];


        $this->options = array_values($this->currentOptions);

        return $this->options;
    }
}
