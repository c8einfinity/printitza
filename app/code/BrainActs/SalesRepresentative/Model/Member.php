<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;
use BrainActs\SalesRepresentative\Api\Data\MemberInterface;

/**
 * Class Member
 * @author BrainActs Core Team <support@brainacts.com>
 *
 * @method getOrderRateType()
 * @method getOrderValue()
 */
class Member extends AbstractModel implements MemberInterface, IdentityInterface
{

    /**
     * Cache tag
     */
    const CACHE_TAG = 'salesrep_member';

    /**#@+
     * Member's statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    /**#@-*/
    /**
     * @var string
     */
    protected $_cacheTag = 'salesrep_member';//@codingStandardsIgnoreLine

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'salesrep_member';//@codingStandardsIgnoreLine

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()//@codingStandardsIgnoreLine
    {
        $this->_init(ResourceModel\Member::class);
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
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData(self::MEMBER_ID);
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getMemberId()
    {
        return $this->getData(self::MEMBER_ID);
    }

    /**
     * Get First Name
     *
     * @return string|null
     */
    public function getFirstname()
    {
        return $this->getData(self::FIRSTNAME);
    }

    /**
     * Get Last Name
     *
     * @return string|null
     */
    public function getLastname()
    {
        return $this->getData(self::LASTNAME);
    }

    /**
     * Get User Id
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->getData(self::USER_ID);
    }

    /**
     * Is active
     *
     * @return bool|null
     */
    public function isActive()
    {
        return $this->getData(self::IS_ACTIVE);
    }

    /**
     * Get Product Priority
     *
     * @return int
     */
    public function getProductPriority()
    {
        return $this->getData(self::PRODUCT_PRIORITY);
    }

    /**
     * Get Product Rate
     *
     * @return int
     */
    public function getProductRateType()
    {
        return $this->getData(self::PRODUCT_RATE_TYPE);
    }

    /**
     * Get Product Rate Value
     *
     * @return double
     */
    public function getProductValue()
    {
        return $this->getData(self::PRODUCT_VALUE);
    }

    /**
     * Get Customer Priority
     *
     * @return int
     */
    public function getCustomerPriority()
    {
        return $this->getData(self::CUSTOMER_PRIORITY);
    }

    /**
     * Get Customer Rate
     *
     * @return int
     */
    public function getCustomerRateType()
    {
        return $this->getData(self::CUSTOMER_RATE_TYPE);
    }

    /**
     * Get Customer Rate Value
     *
     * @return double
     */
    public function getCustomerValue()
    {
        return $this->getData(self::CUSTOMER_VALUE);
    }

    /**
     * Get Region Priority
     *
     * @return int
     */
    public function getRegionPriority()
    {
        return $this->getData(self::REGION_PRIORITY);
    }

    /**
     * Get Region Rate
     *
     * @return int
     */
    public function getRegionRateType()
    {
        return $this->getData(self::REGION_RATE_TYPE);
    }

    /**
     * Get Region Rate Value
     *
     * @return double
     */
    public function getRegionValue()
    {
        return $this->getData(self::REGION_VALUE);
    }

    public function setMemberId($id)
    {
        return $this->setData(self::MEMBER_ID, $id);
    }

    /**
     * Set Firstname
     *
     * @param $firstname
     * @return MemberInterface
     */
    public function setFirstname($firstname)
    {
        return $this->setData(self::FIRSTNAME, $firstname);
    }

    /**
     * Set Last Name
     * @param $lastname
     * @return MemberInterface
     */
    public function setLastname($lastname)
    {
        return $this->setData(self::LASTNAME, $lastname);
    }

    /**
     * Set User Id
     *
     * @param $userId
     * @return MemberInterface
     */
    public function setUserId($userId)
    {
        return $this->setData(self::USER_ID, $userId);
    }

    /**
     * Set Is active
     *
     * @param $isActive
     * @return MemberInterface
     */
    public function setIsActive($isActive)
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }

    /**
     * Get Product Priority
     *
     * @param $productPriority
     * @return MemberInterface
     */
    public function setProductPriority($productPriority)
    {
        return $this->setData(self::PRODUCT_PRIORITY, $productPriority);
    }

    /**
     * Set Product Rate
     *
     * @param $productRateType
     * @return MemberInterface
     */
    public function setProductRateType($productRateType)
    {
        return $this->setData(self::PRODUCT_RATE_TYPE, $productRateType);
    }

    /**
     * Set Product Rate Value
     *
     * @param $productValue
     * @return MemberInterface
     */
    public function setProductValue($productValue)
    {
        return $this->setData(self::PRODUCT_VALUE, $productValue);
    }

    /**
     * Set Customer Priority
     *
     * @param $customerPriority
     * @return MemberInterface
     */
    public function setCustomerPriority($customerPriority)
    {
        return $this->setData(self::CUSTOMER_PRIORITY, $customerPriority);
    }

    /**
     * Set Customer Rate
     *
     * @param $customerRateType
     * @return MemberInterface
     */
    public function setCustomerRateType($customerRateType)
    {
        return $this->setData(self::CUSTOMER_RATE_TYPE, $customerRateType);
    }

    /**
     * Set Customer Rate Value
     *
     * @param $customerValue
     * @return MemberInterface
     */
    public function setCustomerValue($customerValue)
    {
        return $this->setData(self::CUSTOMER_VALUE, $customerValue);
    }

    /**
     * Set Region Priority
     *
     * @param $regionPriority
     * @return MemberInterface
     */
    public function setRegionPriority($regionPriority)
    {
        return $this->setData(self::REGION_PRIORITY, $regionPriority);
    }

    /**
     * Set Region Rate
     *
     * @param $regionRateType
     * @return MemberInterface
     */
    public function setRegionRateType($regionRateType)
    {
        return $this->setData(self::REGION_RATE_TYPE, $regionRateType);
    }

    /**
     * Set Region Rate Value
     *
     * @param $regionValue
     * @return MemberInterface
     */
    public function setRegionValue($regionValue)
    {
        return $this->setData(self::REGION_VALUE, $regionValue);
    }
}
