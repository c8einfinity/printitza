<?php


namespace Designnbuy\Workflow\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface GroupRepositoryInterface
{


    /**
     * Save Group
     * @param \Designnbuy\Workflow\Api\Data\GroupInterface $group
     * @return \Designnbuy\Workflow\Api\Data\GroupInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    
    public function save(
        \Designnbuy\Workflow\Api\Data\GroupInterface $group
    );

    /**
     * Retrieve Group
     * @param string $groupId
     * @return \Designnbuy\Workflow\Api\Data\GroupInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    
    public function getById($groupId);

    /**
     * Retrieve Group matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Designnbuy\Workflow\Api\Data\GroupSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Group
     * @param \Designnbuy\Workflow\Api\Data\GroupInterface $group
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    
    public function delete(
        \Designnbuy\Workflow\Api\Data\GroupInterface $group
    );

    /**
     * Delete Group by ID
     * @param string $groupId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    
    public function deleteById($groupId);
}
