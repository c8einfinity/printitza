<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Model\ResourceModel\OrderTicket;

use Designnbuy\OrderTicket\Api\Data\OrderTicketSearchResultInterface;
use Magento\Sales\Model\ResourceModel\Collection\AbstractCollection;

/**
 * ORDERTICKET entity collection
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Collection extends AbstractCollection implements OrderTicketSearchResultInterface
{
    /**
     * Internal constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Designnbuy\OrderTicket\Model\OrderTicket', 'Designnbuy\OrderTicket\Model\ResourceModel\OrderTicket');
    }
}
