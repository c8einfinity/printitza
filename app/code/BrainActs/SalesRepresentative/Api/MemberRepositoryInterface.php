<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace BrainActs\SalesRepresentative\Api;

/**
 * Sales Representative Member CRUD interface.
 * @api
 */
interface MemberRepositoryInterface
{
    /**
     * Save block.
     *
     * @param \BrainActs\SalesRepresentative\Api\Data\MemberInterface $member
     * @return \BrainActs\SalesRepresentative\Api\Data\MemberInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(Data\MemberInterface $member);

    /**
     * Retrieve member.
     *
     * @param int $memberId
     * @return \BrainActs\SalesRepresentative\Api\Data\MemberInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($memberId);

    /**
     * Retrieve member.
     *
     * @param int $userId
     * @return \BrainActs\SalesRepresentative\Api\Data\MemberInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByUserId($userId);

    /**
     * Retrieve members matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \BrainActs\SalesRepresentative\Api\Data\MemberSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete member.
     *
     * @param \BrainActs\SalesRepresentative\Api\Data\MemberInterface $member
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(Data\MemberInterface $member);

    /**
     * Delete member by ID.
     *
     * @param int $memberId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($memberId);

    /**
     * Apply member to quote.
     *
     * @param int $cartId The cart ID.
     * @param int $memberId Member ID.
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     * @throws \Magento\Framework\Exception\CouldNotSaveException The specified coupon could not be added.
     */
    public function applyToQuote($cartId, $memberId);
}
