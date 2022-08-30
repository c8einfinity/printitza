<?php


namespace Designnbuy\Workflow\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface StatusRepositoryInterface
{


    /**
     * Save Status
     * @param \Designnbuy\Workflow\Api\Data\StatusInterface $status
     * @return \Designnbuy\Workflow\Api\Data\StatusInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    
    public function save(
        \Designnbuy\Workflow\Api\Data\StatusInterface $status
    );

    /**
     * Retrieve Status
     * @param string $statusId
     * @return \Designnbuy\Workflow\Api\Data\StatusInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    
    public function getById($statusId);

    /**
     * Retrieve Status matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Designnbuy\Workflow\Api\Data\StatusSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Status
     * @param \Designnbuy\Workflow\Api\Data\StatusInterface $status
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    
    public function delete(
        \Designnbuy\Workflow\Api\Data\StatusInterface $status
    );

    /**
     * Delete Status by ID
     * @param string $statusId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    
    public function deleteById($statusId);
}
