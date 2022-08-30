<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Api\Data;

/**
 * Interface OrderTicketInterface
 * @api
 */
interface OrderTicketInterface extends \Magento\Framework\Api\CustomAttributesDataInterface
{
    /**
     * Get entity_id
     *
     * @return string
     */
    public function getIncrementId();

    /**
     * Set entity_id
     *
     * @param string $incrementId
     * @return \Designnbuy\OrderTicket\Api\Data\OrderTicketInterface
     */
    public function setIncrementId($incrementId);

    /**
     * Get entity_id
     *
     * @return int
     */
    public function getEntityId();

    /**
     * Set entity_id
     *
     * @param int $entityId
     * @return \Designnbuy\OrderTicket\Api\Data\OrderTicketInterface
     */
    public function setEntityId($entityId);

    /**
     * Get order_id
     *
     * @return int
     */
    public function getOrderId();

    /**
     * Set order_id
     *
     * @param int $orderId
     * @return \Designnbuy\OrderTicket\Api\Data\OrderTicketInterface
     */
    public function setOrderId($orderId);

    /**
     * Get order_increment_id
     *
     * @return string
     */
    public function getOrderIncrementId();

    /**
     * Set order_increment_id
     *
     * @param string $incrementId
     * @return \Designnbuy\OrderTicket\Api\Data\OrderTicketInterface
     */
    public function setOrderIncrementId($incrementId);

    /**
     * Get store_id
     *
     * @return int
     */
    public function getStoreId();

    /**
     * Set store_id
     *
     * @param int $storeId
     * @return \Designnbuy\OrderTicket\Api\Data\OrderTicketInterface
     */
    public function setStoreId($storeId);

    /**
     * Get customer_id
     *
     * @return int
     */
    public function getCustomerId();

    /**
     * Set customer_id
     *
     * @param int $customerId
     * @return \Designnbuy\OrderTicket\Api\Data\OrderTicketInterface
     */
    public function setCustomerId($customerId);

    /**
     * Get date_requested
     *
     * @return string
     */
    public function getDateRequested();

    /**
     * Set date_requested
     *
     * @param string $dateRequested
     * @return \Designnbuy\OrderTicket\Api\Data\OrderTicketInterface
     */
    public function setDateRequested($dateRequested);

    /**
     * Get customer_custom_email
     *
     * @return string
     */
    public function getCustomerCustomEmail();

    /**
     * Set customer_custom_email
     *
     * @param string $customerCustomEmail
     * @return \Designnbuy\OrderTicket\Api\Data\OrderTicketInterface
     */
    public function setCustomerCustomEmail($customerCustomEmail);


    /**
     * Get status
     *
     * @return string
     */
    public function getStatus();

    /**
     * Set status
     *
     * @param string $status
     * @return \Designnbuy\OrderTicket\Api\Data\OrderTicketInterface
     */
    public function setStatus($status);

    /**
     * Get comments list
     *
     * @return \Designnbuy\OrderTicket\Api\Data\CommentInterface[]
     */
    public function getComments();

    /**
     * Set comments list
     *
     * @param \Designnbuy\OrderTicket\Api\Data\CommentInterface[] $comments
     * @return \Designnbuy\OrderTicket\Api\Data\OrderTicketInterface
     */
    public function setComments(array $comments = null);

    /**
     * Get tracks list
     *
     * @return \Designnbuy\OrderTicket\Api\Data\TrackInterface[]
     */
    public function getTracks();

    /**
     * Set tracks list
     *
     * @param \Designnbuy\OrderTicket\Api\Data\TrackInterface[] $tracks
     * @return \Designnbuy\OrderTicket\Api\Data\OrderTicketInterface
     */
    public function setTracks(array $tracks = null);
}
