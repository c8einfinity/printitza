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
interface CommentManagementInterface
{
    /**
     * Add comment
     *
     * @param \Designnbuy\OrderTicket\Api\Data\CommentInterface $data
     * @return bool
     * @throws \Exception
     */
    public function addComment(\Designnbuy\OrderTicket\Api\Data\CommentInterface $data);

    /**
     * Comments list
     *
     * @param int $id
     * @return \Designnbuy\OrderTicket\Api\Data\CommentSearchResultInterface
     */
    public function commentsList($id);
}
