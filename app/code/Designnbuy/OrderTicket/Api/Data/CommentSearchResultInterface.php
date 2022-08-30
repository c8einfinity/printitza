<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Api\Data;

/**
 * Interface CommentSearchResultInterface
 * @api
 */
interface CommentSearchResultInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get OrderTicket Status History list
     *
     * @return \Designnbuy\OrderTicket\Api\Data\CommentInterface[]
     */
    public function getItems();

    /**
     * Set OrderTicket Status History list
     *
     * @param \Designnbuy\OrderTicket\Api\Data\CommentInterface[] $items
     * @return $this
     */
    public function setItems(array $items = null);
}
