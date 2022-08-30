<?php


namespace Designnbuy\Workflow\Model;

use Designnbuy\Workflow\Api\RoleRepositoryInterface;
use Designnbuy\Workflow\Api\Data\RoleSearchResultsInterfaceFactory;
use Designnbuy\Workflow\Api\Data\RoleInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Designnbuy\Workflow\Model\ResourceModel\Role as ResourceRole;
use Designnbuy\Workflow\Model\ResourceModel\Role\CollectionFactory as RoleCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class RoleRepository implements RoleRepositoryInterface
{

    protected $resource;

    protected $roleFactory;

    protected $roleCollectionFactory;

    protected $searchResultsFactory;

    protected $dataObjectHelper;

    protected $dataObjectProcessor;

    protected $dataRoleFactory;

    private $storeManager;


    /**
     * @param ResourceRole $resource
     * @param RoleFactory $roleFactory
     * @param RoleInterfaceFactory $dataRoleFactory
     * @param RoleCollectionFactory $roleCollectionFactory
     * @param RoleSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceRole $resource,
        RoleFactory $roleFactory,
        RoleInterfaceFactory $dataRoleFactory,
        RoleCollectionFactory $roleCollectionFactory,
        RoleSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->roleFactory = $roleFactory;
        $this->roleCollectionFactory = $roleCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataRoleFactory = $dataRoleFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Designnbuy\Workflow\Api\Data\RoleInterface $role
    ) {
        /* if (empty($role->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $role->setStoreId($storeId);
        } */
        try {
            $role->getResource()->save($role);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the role: %1',
                $exception->getMessage()
            ));
        }
        return $role;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($roleId)
    {
        $role = $this->roleFactory->create();
        $role->getResource()->load($role, $roleId);
        if (!$role->getId()) {
            throw new NoSuchEntityException(__('Role with id "%1" does not exist.', $roleId));
        }
        return $role;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->roleCollectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'store_id') {
                    $collection->addStoreFilter($filter->getValue(), false);
                    continue;
                }
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }
        
        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());
        
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setTotalCount($collection->getSize());
        $searchResults->setItems($collection->getItems());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Designnbuy\Workflow\Api\Data\RoleInterface $role
    ) {
        try {
            $role->getResource()->delete($role);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Role: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($roleId)
    {
        return $this->delete($this->getById($roleId));
    }
}
