<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Model\Source;

use BrainActs\SalesRepresentative\Model\Withdrawals;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class WithdrawalStatus
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class WithdrawalStatus implements OptionSourceInterface
{
    /**
     * @var Withdrawals
     */
    public $withdrawals;

    /**
     * WithdrawalStatus constructor.
     * @param Withdrawals $withdrawals
     */
    public function __construct(Withdrawals $withdrawals)
    {
        $this->withdrawals = $withdrawals;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $availableOptions = $this->withdrawals->getAvailableStatuses();
        $options = [];
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }
}
