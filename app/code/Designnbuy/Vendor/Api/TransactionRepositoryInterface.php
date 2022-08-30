<?php


namespace Designnbuy\Vendor\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface TransactionRepositoryInterface
{


    /**
     * Save Transaction
     * @param \Designnbuy\Vendor\Api\Data\TransactionInterface $transaction
     * @return \Designnbuy\Vendor\Api\Data\TransactionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    
    public function save(
        \Designnbuy\Vendor\Api\Data\TransactionInterface $transaction
    );

    /**
     * Retrieve Transaction
     * @param string $transactionId
     * @return \Designnbuy\Vendor\Api\Data\TransactionInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    
    public function getById($transactionId);

    /**
     * Retrieve Transaction matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Designnbuy\Vendor\Api\Data\TransactionSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Transaction
     * @param \Designnbuy\Vendor\Api\Data\TransactionInterface $transaction
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    
    public function delete(
        \Designnbuy\Vendor\Api\Data\TransactionInterface $transaction
    );

    /**
     * Delete Transaction by ID
     * @param string $transactionId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    
    public function deleteById($transactionId);
}
