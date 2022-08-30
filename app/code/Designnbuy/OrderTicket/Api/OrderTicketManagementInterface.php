<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Api;

/**
 * Interface OrderTicketManagementInterface
 * @api
 */
interface OrderTicketManagementInterface
{
    /**
     * Save ORDERTICKET
     *
     * @param \Designnbuy\OrderTicket\Api\Data\OrderTicketInterface $orderticketDataObject
     * @return \Designnbuy\OrderTicket\Api\Data\OrderTicketInterface
     */
    public function saveOrderTicket(\Designnbuy\OrderTicket\Api\Data\OrderTicketInterface $orderticketDataObject);

    /**
     * Return list of orderticket data objects based on search criteria
     *
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria
     * @return \Designnbuy\OrderTicket\Api\Data\OrderTicketSearchResultInterface
     */
    public function search(\Magento\Framework\Api\SearchCriteria $searchCriteria);
}
