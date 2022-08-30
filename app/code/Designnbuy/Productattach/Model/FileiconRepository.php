<?php
namespace Designnbuy\Productattach\Model;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;
use Designnbuy\Productattach\Model\ResourceModel\Fileicon as ResourceFileicon;
use Magento\Framework\Reflection\DataObjectProcessor;
use Designnbuy\Productattach\Api\Data\FileiconSearchResultsInterfaceFactory;
use Designnbuy\Productattach\Model\ResourceModel\Fileicon\CollectionFactory as FileiconCollectionFactory;
use Magento\Framework\Api\SortOrder;
use Designnbuy\Productattach\Api\FileiconRepositoryInterface;
use Designnbuy\Productattach\Api\Data\FileiconInterfaceFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\DataObjectHelper;

class FileiconRepository implements fileiconRepositoryInterface
{

    private $storeManager;

    protected $dataObjectProcessor;

    protected $dataObjectHelper;

    protected $searchResultsFactory;

    protected $resource;

    protected $fileiconCollectionFactory;

    protected $fileiconFactory;

    protected $dataFileiconFactory;


    /**
     * @param ResourceFileicon $resource
     * @param FileiconFactory $fileiconFactory
     * @param FileiconInterfaceFactory $dataFileiconFactory
     * @param FileiconCollectionFactory $fileiconCollectionFactory
     * @param FileiconSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ResourceFileicon $resource,
        FileiconFactory $fileiconFactory,
        FileiconInterfaceFactory $dataFileiconFactory,
        FileiconCollectionFactory $fileiconCollectionFactory,
        FileiconSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->fileiconFactory = $fileiconFactory;
        $this->fileiconCollectionFactory = $fileiconCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataFileiconFactory = $dataFileiconFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Designnbuy\Productattach\Api\Data\FileiconInterface $fileicon
    ) {
        /* if (empty($fileicon->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $fileicon->setStoreId($storeId);
        } */
        try {
            $fileicon->getResource()->save($fileicon);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the fileicon: %1',
                $exception->getMessage()
            ));
        }
        return $fileicon;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($fileiconId)
    {
        $fileicon = $this->fileiconFactory->create();
        $fileicon->getResource()->load($fileicon, $fileiconId);
        if (!$fileicon->getId()) {
            throw new NoSuchEntityException(__('Fileicon with id "%1" does not exist.', $fileiconId));
        }
        return $fileicon;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->fileiconCollectionFactory->create();
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
        \Designnbuy\Productattach\Api\Data\FileiconInterface $fileicon
    ) {
        try {
            $fileicon->getResource()->delete($fileicon);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Fileicon: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($fileiconId)
    {
        return $this->delete($this->getById($fileiconId));
    }
}
