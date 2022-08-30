<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Admin ORDERTICKET create order block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Designnbuy\OrderTicket\Block\Adminhtml\OrderTicket\Create;

class Order extends \Designnbuy\OrderTicket\Block\Adminhtml\OrderTicket\Create\AbstractCreate
{
    /**
     * Get Header Text for Order Selection
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __('Please Select Order');
    }
}
