<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Api\Data;

/**
 * Interface for member.
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 * @api
 */
interface MemberInterface
{
    const MEMBER_ID = 'member_id';
    const FIRSTNAME = 'firstname';
    const LASTNAME = 'lastname';
    const USER_ID = 'user_id';
    const IS_ACTIVE = 'is_active';
    const PRODUCT_PRIORITY = 'product_priority';
    const PRODUCT_RATE_TYPE = 'product_rate_type';
    const PRODUCT_VALUE = 'product_value';
    const CUSTOMER_PRIORITY = 'customer_priority';
    const CUSTOMER_RATE_TYPE = 'customer_rate_type';
    const CUSTOMER_VALUE = 'customer_value';
    const REGION_PRIORITY = 'region_priority';
    const REGION_RATE_TYPE = 'region_rate_type';
    const REGION_VALUE = 'region_value';

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get Member ID
     *
     * @return int|null
     */
    public function getMemberId();

    /**
     * Get First Name
     *
     * @return string|null
     */
    public function getFirstname();

    /**
     * Get Last Name
     *
     * @return string|null
     */
    public function getLastname();

    /**
     * Get User Id
     *
     * @return int
     */
    public function getUserId();

    /**
     * Is active
     *
     * @return bool|null
     */
    public function isActive();

    /**
     * Get Product Priority
     *
     * @return int
     */
    public function getProductPriority();

    /**
     * Get Product Rate
     *
     * @return int
     */
    public function getProductRateType();

    /**
     * Get Product Rate Value
     *
     * @return double
     */
    public function getProductValue();

    /**
     * Get Customer Priority
     *
     * @return int
     */
    public function getCustomerPriority();

    /**
     * Get Customer Rate
     *
     * @return int
     */
    public function getCustomerRateType();

    /**
     * Get Customer Rate Value
     *
     * @return double
     */
    public function getCustomerValue();

    /**
     * Get Region Priority
     *
     * @return int
     */
    public function getRegionPriority();

    /**
     * Get Region Rate
     *
     * @return int
     */
    public function getRegionRateType();

    /**
     * Get Region Rate Value
     *
     * @return double
     */
    public function getRegionValue();

    /**
     * Set ID
     *
     * @param int $id
     * @return MemberInterface
     */
    public function setMemberId($id);

    /**
     * Set Firstname
     *
     * @param $firstname
     * @return MemberInterface
     */
    public function setFirstname($firstname);

    /**
     * Set Last Name
     * @param $lastname
     * @return MemberInterface
     */
    public function setLastname($lastname);

    /**
     * Set User Id
     *
     * @param $userId
     * @return MemberInterface
     */
    public function setUserId($userId);

    /**
     * Set Is active
     *
     * @param $isActive
     * @return MemberInterface
     */
    public function setIsActive($isActive);

    /**
     * Get Product Priority
     *
     * @param $productPriority
     * @return MemberInterface
     */
    public function setProductPriority($productPriority);

    /**
     * Set Product Rate
     *
     * @param $productRateType
     * @return MemberInterface
     */
    public function setProductRateType($productRateType);

    /**
     * Set Product Rate Value
     *
     * @param $productValue
     * @return MemberInterface
     */
    public function setProductValue($productValue);

    /**
     * Set Customer Priority
     *
     * @param $customerPriority
     * @return MemberInterface
     */
    public function setCustomerPriority($customerPriority);

    /**
     * Set Customer Rate
     *
     * @param $customerRateType
     * @return MemberInterface
     */
    public function setCustomerRateType($customerRateType);

    /**
     * Set Customer Rate Value
     *
     * @param $customerValue
     * @return MemberInterface
     */
    public function setCustomerValue($customerValue);

    /**
     * Set Region Priority
     *
     * @param $regionPriority
     * @return MemberInterface
     */
    public function setRegionPriority($regionPriority);

    /**
     * Set Region Rate
     *
     * @param $regionRateType
     * @return MemberInterface
     */
    public function setRegionRateType($regionRateType);

    /**
     * Set Region Rate Value
     *
     * @param $regionValue
     * @return MemberInterface
     */
    public function setRegionValue($regionValue);
}
