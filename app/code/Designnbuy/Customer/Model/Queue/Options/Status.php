<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Customer Queue statuses option array
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Designnbuy\Customer\Model\Queue\Options;

class Status implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Return statuses option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            \Designnbuy\Customer\Model\Queue::STATUS_SENT => __('Sent'),
            \Designnbuy\Customer\Model\Queue::STATUS_CANCEL => __('Canceled'),
            \Designnbuy\Customer\Model\Queue::STATUS_NEVER => __('Not Sent'),
            \Designnbuy\Customer\Model\Queue::STATUS_SENDING => __('Sending'),
            \Designnbuy\Customer\Model\Queue::STATUS_PAUSE => __('Paused')
        ];
    }
}
