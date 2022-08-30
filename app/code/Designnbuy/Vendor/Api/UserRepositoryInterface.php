<?php


namespace Designnbuy\Vendor\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface UserRepositoryInterface
{


    /**
     * Save User
     * @param \Designnbuy\Vendor\Api\Data\UserInterface $user
     * @return \Designnbuy\Vendor\Api\Data\UserInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    
    public function save(
        \Designnbuy\Vendor\Api\Data\UserInterface $user
    );

    /**
     * Retrieve User
     * @param string $userId
     * @return \Designnbuy\Vendor\Api\Data\UserInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    
    public function getById($userId);

    /**
     * Retrieve User matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Designnbuy\Vendor\Api\Data\UserSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete User
     * @param \Designnbuy\Vendor\Api\Data\UserInterface $user
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    
    public function delete(
        \Designnbuy\Vendor\Api\Data\UserInterface $user
    );

    /**
     * Delete User by ID
     * @param string $userId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    
    public function deleteById($userId);
}
