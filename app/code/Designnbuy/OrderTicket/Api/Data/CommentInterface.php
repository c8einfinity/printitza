<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Designnbuy\OrderTicket\Api\Data;

/**
 * Interface CommentInterface
 * @api
 */
interface CommentInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{
    /**
     * Returns comment
     *
     * @return string
     */
    public function getComment();

    /**
     * Set comment
     *
     * @param string $comment
     * @return \Designnbuy\OrderTicket\Api\Data\CommentInterface
     */
    public function setComment($comment);

    /**
     * Return OrderTicket Id
     *
     * @return int
     */
    public function getOrderTicketEntityId();

    /**
     * Set OrderTicket Id
     *
     * @param int $orderticketId
     * @return \Designnbuy\OrderTicket\Api\Data\CommentInterface
     */
    public function setOrderTicketEntityId($orderticketId);

    /**
     * Returns created_at
     *
     * @return string
     */
    public function getCreatedAt();

    /**
     * Set created_at
     *
     * @param string $createdAt
     * @return \Designnbuy\OrderTicket\Api\Data\CommentInterface
     */
    public function setCreatedAt($createdAt);

    /**
     * Returns entity_id
     *
     * @return int
     */
    public function getEntityId();

    /**
     * Set entity_id
     *
     * @param int $entityId
     * @return \Designnbuy\OrderTicket\Api\Data\CommentInterface
     */
    public function setEntityId($entityId);

    /**
     * Returns is_customer_notified
     *
     * @return bool
     */
    public function isCustomerNotified();

    /**
     * Set is_customer_notified
     *
     * @param bool $isCustomerNotified
     * @return \Designnbuy\OrderTicket\Api\Data\CommentInterface
     */
    public function setIsCustomerNotified($isCustomerNotified);

    /**
     * Returns is_visible_on_front
     *
     * @return bool
     */
    public function isVisibleOnFront();

    /**
     * Set is_visible_on_front
     *
     * @param bool $isVisibleOnFront
     * @return \Designnbuy\OrderTicket\Api\Data\CommentInterface
     */
    public function setIsVisibleOnFront($isVisibleOnFront);

    /**
     * Returns status
     *
     * @return string
     */
    public function getStatus();

    /**
     * Set status
     *
     * @param string $status
     * @return \Designnbuy\OrderTicket\Api\Data\CommentInterface
     */
    public function setStatus($status);

    /**
     * Returns is_admin
     *
     * @return bool
     */
    public function isAdmin();

    /**
     * Set is_admin
     *
     * @param bool $isAdmin
     * @return \Designnbuy\OrderTicket\Api\Data\CommentInterface
     */
    public function setIsAdmin($isAdmin);

}
