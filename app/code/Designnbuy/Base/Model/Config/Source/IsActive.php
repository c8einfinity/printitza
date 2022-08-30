<?php

/**
 *
 * Copyright © 2015 Designnbuy. All rights reserved.
 * See COPYING.txt for license details.
 *
 */

namespace Designnbuy\Base\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class IsActive implements OptionSourceInterface
{

    const STATUS_ENABLED = 1;

    const STATUS_DISABLED = 0;

    protected $image;

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $availableOptions = $this->getAvailableStatuses();
        $options = [];
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }

    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }
}
