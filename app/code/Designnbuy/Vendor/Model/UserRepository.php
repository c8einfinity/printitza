<?php


namespace Designnbuy\Vendor\Model;

use Designnbuy\Vendor\Api\UserRepositoryInterface;
use Designnbuy\Vendor\Api\Data\UserSearchResultsInterfaceFactory;
use Designnbuy\Vendor\Api\Data\UserInterfaceFactory;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Designnbuy\Vendor\Model\ResourceModel\User as ResourceUser;
use Designnbuy\Vendor\Model\ResourceModel\User\CollectionFactory as UserCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

class UserRepository implements UserRepositoryInterface
{

    protected $resource;

    protected $userFactory;

    protected $userCollectionFactory;

    protected $searchResultsFactory;

    protected $dataObjectHelper;

    protected $dataObjectProcessor;

    protected $dataUserFactory;

    private $storeManager;


    /**
     * @param ResourceUser $resource
     * @param UserFactory $userFactory
     * @param UserInterfaceFactory $dataUserFactory
     * @param UserCollectionFactory $userCollectionFactory
     * @param UserSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceUser $resource,
        UserFactory $userFactory,
        UserInterfaceFactory $dataUserFactory,
        UserCollectionFactory $userCollectionFactory,
        UserSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->userFactory = $userFactory;
        $this->userCollectionFactory = $userCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataUserFactory = $dataUserFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Designnbuy\Vendor\Api\Data\UserInterface $user
    ) {
        /* if (empty($user->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $user->setStoreId($storeId);
        } */
        try {
            $user->getResource()->save($user);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the user: %1',
                $exception->getMessage()
            ));
        }
        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($userId)
    {
        $user = $this->userFactory->create();
        $user->getResource()->load($user, $userId);
        if (!$user->getId()) {
            throw new NoSuchEntityException(__('User with id "%1" does not exist.', $userId));
        }
        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->userCollectionFactory->create();
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
        \Designnbuy\Vendor\Api\Data\UserInterface $user
    ) {
        try {
            $user->getResource()->delete($user);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the User: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($userId)
    {
        return $this->delete($this->getById($userId));
    }
}
