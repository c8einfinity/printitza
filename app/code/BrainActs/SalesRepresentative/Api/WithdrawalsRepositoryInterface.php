<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace BrainActs\SalesRepresentative\Api;

/**
 * Sales Representative Withdrawals CRUD interface.
 * @api
 */
interface WithdrawalsRepositoryInterface
{
    /**
     * Save withdrawal.
     *
     * @param \BrainActs\SalesRepresentative\Api\Data\WithdrawalsInterface $withdrawal
     * @return \BrainActs\SalesRepresentative\Api\Data\WithdrawalsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(Data\WithdrawalsInterface $withdrawal);

    /**
     * Retrieve withdrawal.
     *
     * @param int $withdrawalId
     * @return \BrainActs\SalesRepresentative\Api\Data\WithdrawalsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($withdrawalId);

    /**
     * Retrieve withdrawals matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \BrainActs\SalesRepresentative\Api\Data\WithdrawalsSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete withdrawal.
     *
     * @param \BrainActs\SalesRepresentative\Api\Data\WithdrawalsInterface $withdrawal
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(Data\WithdrawalsInterface $withdrawal);

    /**
     * Delete withdrawal by ID.
     *
     * @param int $withdrawalId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($withdrawalId);
}
