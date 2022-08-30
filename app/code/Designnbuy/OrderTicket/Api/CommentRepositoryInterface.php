<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Api;

/**
 * Interface CommentRepositoryInterface
 * @api
 */
interface CommentRepositoryInterface
{
    /**
     * Get comment by id
     *
     * @param int $id
     * @return \Designnbuy\OrderTicket\Api\Data\CommentSearchResultInterface
     */
    public function get($id);

    /**
     * Get comments list
     *
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria
     * @return \Designnbuy\OrderTicket\Api\Data\CommentSearchResultInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Save comment
     *
     * @param \Designnbuy\OrderTicket\Api\Data\CommentInterface $comment
     * @return bool
     */
    public function save(\Designnbuy\OrderTicket\Api\Data\CommentInterface $comment);

    /**
     * Delete comment
     *
     * @param \Designnbuy\OrderTicket\Api\Data\CommentInterface $comment
     * @return bool
     */
    public function delete(\Designnbuy\OrderTicket\Api\Data\CommentInterface $comment);
}
