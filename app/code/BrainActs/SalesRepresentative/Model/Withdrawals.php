<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;
use BrainActs\SalesRepresentative\Api\Data\WithdrawalsInterface;

/**
 * Class Withdrawals
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class Withdrawals extends AbstractModel implements WithdrawalsInterface, IdentityInterface
{

    /**
     * Cache tag
     */
    const CACHE_TAG = 'salesrep_withdrawals';

    /**#@+
     * Member's statuses
     */
    const STATUS_PENDING = 1;
    const STATUS_CANCELED = 2;
    const STATUS_COMPLETED = 3;

    /**#@-*/
    /**
     * @var string
     */
    protected $_cacheTag = 'salesrep_withdrawal';//@codingStandardsIgnoreLine

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'salesrep_withdrawal';//@codingStandardsIgnoreLine

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()//@codingStandardsIgnoreLine
    {
        $this->_init(ResourceModel\Withdrawals::class);
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId(), self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Prepare member's statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [
            self::STATUS_PENDING => __('Pending'),
            self::STATUS_CANCELED => __('Canceled'),
            self::STATUS_COMPLETED => __('Completed')
        ];
    }

    /**
     * Get Amount
     *
     * @return float|null
     */
    public function getAmount()
    {
        return $this->getData(self::AMOUNT);
    }

    /**
     * Get Member ID
     *
     * @return string|null
     */
    public function getMemberId()
    {
        return $this->getData(self::MEMBER_ID);
    }

    /**
     * Get Creation Time
     *
     * @return int
     */
    public function getCreationTime()
    {
        return $this->getData(self::CREATION_TIME);
    }

    /**
     * Get Status
     *
     * @return bool
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * Set Amount
     *
     * @param $amount
     * @return WithdrawalsInterface
     */
    public function setAmount($amount)
    {
        return $this->setData(self::AMOUNT, $amount);
    }

    /**
     * Set Member ID
     * @param $memberId
     * @return WithdrawalsInterface
     */
    public function setMemberId($memberId)
    {
        return $this->setData(self::MEMBER_ID, $memberId);
    }

    /**
     * Set User Id
     *
     * @param $time
     * @return WithdrawalsInterface
     */
    public function setCreationTime($time)
    {
        return $this->setData(self::CREATION_TIME, $time);
    }

    /**
     * Set Status
     *
     * @param $status
     * @return WithdrawalsInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Get Comment
     *
     * @return string|null
     */
    public function getComment()
    {
        return $this->getData(self::COMMENT);
    }

    /**
     * Set Comment
     *
     * @param $comment
     * @return WithdrawalsInterface
     */
    public function setComment($comment)
    {
        return $this->setData(self::COMMENT, $comment);
    }
}
