<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Api\Data;

interface WithdrawalsInterface
{
    const WITHDRAWAL_ID = 'withdrawal_id';
    const AMOUNT = 'amount';
    const MEMBER_ID = 'member_id';
    const CREATION_TIME = 'creation_time';
    const STATUS = 'status';
    const COMMENT = 'comment';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get Amount
     *
     * @return float|null
     */
    public function getAmount();

    /**
     * Get Member ID
     *
     * @return string|null
     */
    public function getMemberId();

    /**
     * Get Creation Time
     *
     * @return int
     */
    public function getCreationTime();

    /**
     * Get Status
     *
     * @return bool
     */
    public function getStatus();

    /**
     * Get Comment
     *
     * @return string|null
     */
    public function getComment();

    /**
     * Set ID
     *
     * @param int $id
     * @return WithdrawalsInterface
     */
    public function setId($id);

    /**
     * Set Amount
     *
     * @param $amount
     * @return WithdrawalsInterface
     */
    public function setAmount($amount);

    /**
     * Set Member ID
     * @param $memberId
     * @return WithdrawalsInterface
     */
    public function setMemberId($memberId);

    /**
     * Set User Id
     *
     * @param $time
     * @return WithdrawalsInterface
     */
    public function setCreationTime($time);

    /**
     * Set Status
     *
     * @param $status
     * @return WithdrawalsInterface
     */
    public function setStatus($status);

    /**
     * Set Comment
     *
     * @param $comment
     * @return WithdrawalsInterface
     */
    public function setComment($comment);
}
