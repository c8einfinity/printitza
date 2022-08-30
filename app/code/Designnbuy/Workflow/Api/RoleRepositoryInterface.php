<?php


namespace Designnbuy\Workflow\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface RoleRepositoryInterface
{


    /**
     * Save Role
     * @param \Designnbuy\Workflow\Api\Data\RoleInterface $role
     * @return \Designnbuy\Workflow\Api\Data\RoleInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    
    public function save(
        \Designnbuy\Workflow\Api\Data\RoleInterface $role
    );

    /**
     * Retrieve Role
     * @param string $roleId
     * @return \Designnbuy\Workflow\Api\Data\RoleInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    
    public function getById($roleId);

    /**
     * Retrieve Role matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Designnbuy\Workflow\Api\Data\RoleSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Role
     * @param \Designnbuy\Workflow\Api\Data\RoleInterface $role
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    
    public function delete(
        \Designnbuy\Workflow\Api\Data\RoleInterface $role
    );

    /**
     * Delete Role by ID
     * @param string $roleId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    
    public function deleteById($roleId);
}
