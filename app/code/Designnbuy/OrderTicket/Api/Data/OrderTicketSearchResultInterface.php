<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Api\Data;

/**
 * Interface OrderTicketSearchResultInterface
 * @api
 */
interface OrderTicketSearchResultInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get OrderTicket list
     *
     * @return \Designnbuy\OrderTicket\Api\Data\OrderTicketInterface[]
     */
    public function getItems();

    /**
     * Set OrderTicket list
     *
     * @param \Designnbuy\OrderTicket\Api\Data\OrderTicketInterface[] $items
     * @return $this
     */
    public function setItems(array $items = null);
}
