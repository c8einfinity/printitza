<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Api\Data;

/**
 * Interface TrackSearchResultInterface
 * @api
 */
interface TrackSearchResultInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get OrderTicket list
     *
     * @return \Designnbuy\OrderTicket\Api\Data\TrackInterface[]
     */
    public function getItems();

    /**
     * Set OrderTicket list
     *
     * @param \Designnbuy\OrderTicket\Api\Data\TrackInterface[] $items
     * @return $this
     */
    public function setItems(array $items = null);
}
