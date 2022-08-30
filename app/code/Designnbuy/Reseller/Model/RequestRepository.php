<?php
/**
 * Designnbuy_Reseller extension
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category  Designnbuy
 * @package   Designnbuy_Reseller
 * @copyright Copyright (c) 2018
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 */
namespace Designnbuy\Reseller\Model;

class RequestRepository implements \Designnbuy\Reseller\Api\RequestRepositoryInterface
{
    /**
     * Cached instances
     * 
     * @var array
     */
    protected $instances = [];

    /**
     * Request resource model
     * 
     * @var \Designnbuy\Reseller\Model\ResourceModel\Request
     */
    protected $resource;

    /**
     * Request collection factory
     * 
     * @var \Designnbuy\Reseller\Model\ResourceModel\Request\CollectionFactory
     */
    protected $requestCollectionFactory;

    /**
     * Request interface factory
     * 
     * @var \Designnbuy\Reseller\Api\Data\RequestInterfaceFactory
     */
    protected $requestInterfaceFactory;

    /**
     * Data Object Helper
     * 
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * Search result factory
     * 
     * @var \Designnbuy\Reseller\Api\Data\RequestSearchResultInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * constructor
     * 
     * @param \Designnbuy\Reseller\Model\ResourceModel\Request $resource
     * @param \Designnbuy\Reseller\Model\ResourceModel\Request\CollectionFactory $requestCollectionFactory
     * @param \Designnbuy\Reseller\Api\Data\RequestInterfaceFactory $requestInterfaceFactory
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @param \Designnbuy\Reseller\Api\Data\RequestSearchResultInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        \Designnbuy\Reseller\Model\ResourceModel\Request $resource,
        \Designnbuy\Reseller\Model\ResourceModel\Request\CollectionFactory $requestCollectionFactory,
        \Designnbuy\Reseller\Api\Data\RequestInterfaceFactory $requestInterfaceFactory,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Designnbuy\Reseller\Api\Data\RequestSearchResultInterfaceFactory $searchResultsFactory
    ) {
        $this->resource                 = $resource;
        $this->requestCollectionFactory = $requestCollectionFactory;
        $this->requestInterfaceFactory  = $requestInterfaceFactory;
        $this->dataObjectHelper         = $dataObjectHelper;
        $this->searchResultsFactory     = $searchResultsFactory;
    }

    /**
     * Save Request.
     *
     * @param \Designnbuy\Reseller\Api\Data\RequestInterface $request
     * @return \Designnbuy\Reseller\Api\Data\RequestInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Designnbuy\Reseller\Api\Data\RequestInterface $request)
    {
        /** @var \Designnbuy\Reseller\Api\Data\RequestInterface|\Magento\Framework\Model\AbstractModel $request */
        try {
            $this->resource->save($request);
        } catch (\Exception $exception) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(__(
                'Could not save the Request: %1',
                $exception->getMessage()
            ));
        }
        return $request;
    }

    /**
     * Retrieve Request.
     *
     * @param int $requestId
     * @return \Designnbuy\Reseller\Api\Data\RequestInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($requestId)
    {
        if (!isset($this->instances[$requestId])) {
            /** @var \Designnbuy\Reseller\Api\Data\RequestInterface|\Magento\Framework\Model\AbstractModel $request */
            $request = $this->requestInterfaceFactory->create();
            $this->resource->load($request, $requestId);
            if (!$request->getId()) {
                throw new \Magento\Framework\Exception\NoSuchEntityException(__('Requested Request doesn\'t exist'));
            }
            $this->instances[$requestId] = $request;
        }
        return $this->instances[$requestId];
    }

    /**
     * Retrieve Requests matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Designnbuy\Reseller\Api\Data\RequestSearchResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        /** @var \Designnbuy\Reseller\Api\Data\RequestSearchResultInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var \Designnbuy\Reseller\Model\ResourceModel\Request\Collection $collection */
        $collection = $this->requestCollectionFactory->create();

        //Add filters from root filter group to the collection
        /** @var \Magento\Framework\Api\Search\FilterGroup $group */
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $collection);
        }
        $sortOrders = $searchCriteria->getSortOrders();
        /** @var \Magento\Framework\Api\SortOrder $sortOrder */
        if ($sortOrders) {
            foreach ($searchCriteria->getSortOrders() as $sortOrder) {
                $field = $sortOrder->getField();
                $collection->addOrder(
                    $field,
                    ($sortOrder->getDirection() == \Magento\Framework\Api\SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        } else {
            // set a default sorting order since this method is used constantly in many
            // different blocks
            $field = 'request_id';
            $collection->addOrder($field, 'ASC');
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());

        /** @var \Designnbuy\Reseller\Api\Data\RequestInterface[] $requests */
        $requests = [];
        /** @var \Designnbuy\Reseller\Model\Request $request */
        foreach ($collection as $request) {
            /** @var \Designnbuy\Reseller\Api\Data\RequestInterface $requestDataObject */
            $requestDataObject = $this->requestInterfaceFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $requestDataObject,
                $requestr->getData(),
                \Designnbuy\Reseller\Api\Data\RequestInterface::class
            );
            $requests[] = $requestDataObject;
        }
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults->setItems($requests);
    }

    /**
     * Delete Request.
     *
     * @param \Designnbuy\Reseller\Api\Data\RequestInterface $request
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Designnbuy\Reseller\Api\Data\RequestInterface $request)
    {
        /** @var \Designnbuy\Reseller\Api\Data\RequestInterface|\Magento\Framework\Model\AbstractModel $request */
        $id = $request->getId();
        try {
            unset($this->instances[$id]);
            $this->resource->delete($request);
        } catch (\Magento\Framework\Exception\ValidatorException $e) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\StateException(
                __('Unable to remove Request %1', $id)
            );
        }
        unset($this->instances[$id]);
        return true;
    }

    /**
     * Delete Request by ID.
     *
     * @param int $requestId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($requestId)
    {
        $request = $this->getById($requestId);
        return $this->delete($request);
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param \Magento\Framework\Api\Search\FilterGroup $filterGroup
     * @param \Designnbuy\Reseller\Model\ResourceModel\Request\Collection $collection
     * @return $this
     * @throws \Magento\Framework\Exception\InputException
     */
    protected function addFilterGroupToCollection(
        \Magento\Framework\Api\Search\FilterGroup $filterGroup,
        \Designnbuy\Reseller\Model\ResourceModel\Request\Collection $collection
    ) {
        $fields = [];
        $conditions = [];
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $fields[] = $filter->getField();
            $conditions[] = [$condition => $filter->getValue()];
        }
        if ($fields) {
            $collection->addFieldToFilter($fields, $conditions);
        }
        return $this;
    }
}
