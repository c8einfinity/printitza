<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Block\Adminhtml\Dashboard\Withdrawals;

/**
 * Class Status
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class Status extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var array
     */
    public static $statuses;

    /**
     * Constructor for Grid Renderer Status
     *
     * @return void
     */
    protected function _construct()//@codingStandardsIgnoreLine
    {
        self::$statuses = [
            \BrainActs\SalesRepresentative\Model\Withdrawals::STATUS_PENDING => __('Pending'),
            \BrainActs\SalesRepresentative\Model\Withdrawals::STATUS_CANCELED => __('Canceled'),
            \BrainActs\SalesRepresentative\Model\Withdrawals::STATUS_COMPLETED=> __('Completed'),

        ];
        parent::_construct();
    }

    /**
     * @param \Magento\Framework\DataObject $row
     * @return \Magento\Framework\Phrase
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        return __($this->getStatus($row->getStatus()));
    }

    /**
     * @param string $status
     * @return \Magento\Framework\Phrase
     */
    public static function getStatus($status)
    {
        if (isset(self::$statuses[$status])) {
            return self::$statuses[$status];
        }

        return __('Unknown');
    }
}
