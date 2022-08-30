<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Api;

/**
 * Interface OrderTicketRepositoryInterface
 * @api
 */
interface OrderTicketRepositoryInterface
{
    /**
     * Return data object for specified ORDERTICKET id
     *
     * @param int $id
     * @return \Designnbuy\OrderTicket\Api\Data\OrderTicketInterface
     */
    public function get($id);

    /**
     * Return list of ORDERTICKET data objects based on search criteria
     *
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria
     * @return \Designnbuy\OrderTicket\Api\Data\OrderTicketSearchResultInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Save ORDERTICKET
     *
     * @param \Designnbuy\OrderTicket\Api\Data\OrderTicketInterface $orderticketDataObject
     * @return \Designnbuy\OrderTicket\Api\Data\OrderTicketInterface
     */
    public function save(\Designnbuy\OrderTicket\Api\Data\OrderTicketInterface $orderticketDataObject);

    /**
     * Delete ORDERTICKET
     *
     * @param \Designnbuy\OrderTicket\Api\Data\OrderTicketInterface $orderticketDataObject
     * @return bool
     */
    public function delete(\Designnbuy\OrderTicket\Api\Data\OrderTicketInterface $orderticketDataObject);
}
