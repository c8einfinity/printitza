<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Model;

use BrainActs\SalesRepresentative\Api\Data;
use BrainActs\SalesRepresentative\Api\MemberRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use BrainActs\SalesRepresentative\Model\ResourceModel\Member as ResourceMember;
use BrainActs\SalesRepresentative\Model\ResourceModel\Member\CollectionFactory as MemberCollectionFactory;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class MemberRepository
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class MemberRepository implements MemberRepositoryInterface
{
    /**
     * @var ResourceMember
     */
    public $resource;

    /**
     * @var MemberFactory
     */
    public $memberFactory;

    /**
     * @var MemberCollectionFactory
     */
    public $memberCollectionFactory;

    /**
     * @var Data\MemberSearchResultsInterfaceFactory
     */
    public $searchResultsFactory;

    /**
     * @var DataObjectHelper
     */
    public $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    public $dataObjectProcessor;

    /**
     * @var \BrainActs\SalesRepresentative\Api\Data\MemberInterfaceFactory
     */
    public $dataMemberFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * Quote repository.
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * MemberRepository constructor.
     * @param ResourceMember $resource
     * @param MemberFactory $memberFactory
     * @param Data\MemberInterfaceFactory $dataMemberFactory
     * @param MemberCollectionFactory $memberCollectionFactory
     * @param Data\MemberSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     */
    public function __construct(
        ResourceMember $resource,
        MemberFactory $memberFactory,
        \BrainActs\SalesRepresentative\Api\Data\MemberInterfaceFactory $dataMemberFactory,
        MemberCollectionFactory $memberCollectionFactory,
        Data\MemberSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
    ) {
    
        $this->resource = $resource;
        $this->memberFactory = $memberFactory;
        $this->memberCollectionFactory = $memberCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataMemberFactory = $dataMemberFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * Save Member data
     * @param Data\MemberInterface $member
     * @return Data\MemberInterface
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function save(Data\MemberInterface $member)
    {
        $storeId = $this->storeManager->getStore()->getId();
        $member->setStoreId($storeId);
        try {
            $this->resource->save($member);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }
        return $member;
    }

    /**
     * Load Member data by given Member Identity
     *
     * @param string $memberId
     * @return Member
     * @throws NoSuchEntityException
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($memberId)
    {
        $member = $this->memberFactory->create();
        $this->resource->load($member, $memberId);
        if (!$member->getId()) {
            throw new NoSuchEntityException(__('Member with id "%1" does not exist.', $memberId));
        }
        return $member;
    }

    /**
     * Load Member data by given admin user Identity
     *
     * @param string $userId
     * @return Member
     * @throws NoSuchEntityException
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getByUserId($userId)
    {
        $member = $this->memberFactory->create();
        $this->resource->load($member, $userId, 'user_id');
        if (!$member->getId()) {
            throw new NoSuchEntityException(__('Member for user id "%1" does not exist.', $userId));
        }
        return $member;
    }

    /**
     * Load Member data collection by given search criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteriaItem
     * @return Data\MemberSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteriaItem)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteriaItem);

        $collection = $this->memberCollectionFactory->create();
        foreach ($criteriaItem->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ?: 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }
        $searchResults->setTotalCount($collection->getSize());
        $sortOrders = $criteriaItem->getSortOrders();
        if ($sortOrders) {
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($criteriaItem->getCurrentPage());
        $collection->setPageSize($criteriaItem->getPageSize());
        $members = [];
        /** @var Member $memberModel */
        foreach ($collection as $memberModel) {
            $memberData = $this->dataMemberFactory->create();

            $this->dataObjectHelper->populateWithArray(
                $memberData,
                $memberModel->getData(),
                Data\MemberInterface::class
            );

            $members[] = $this->dataObjectProcessor->buildOutputDataArray(
                $memberData,
                Data\MemberInterface::class
            );
        }
        $searchResults->setItems($members);
        return $searchResults;
    }

    /**
     * Delete Member
     * @param Data\MemberInterface $member
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(Data\MemberInterface $member)
    {
        try {
            $this->resource->delete($member);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }
        return true;
    }

    /**
     * Delete Member by given Member Identity
     *
     * @param string $memberId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($memberId)
    {
        return $this->delete($this->getById($memberId));
    }

    /**
     * Apply member to quote.
     *
     * @param int $cartId The cart ID.
     * @param int $memberId Member ID.
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     * @throws \Magento\Framework\Exception\CouldNotSaveException The specified coupon could not be added.
     */
    public function applyToQuote($cartId, $memberId)
    {
        /** @var  \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        if (!$quote->getItemsCount()) {
            throw new NoSuchEntityException(__('Cart %1 doesn\'t contain products', $cartId));
        }

        try {
            $quote->setSalesRepresentativeId($memberId);
            $this->quoteRepository->save($quote->collectTotals());
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__('Could not apply sales representative member'));
        }
        if ($quote->getSalesRepresentativeId() != $memberId) {
            throw new NoSuchEntityException(__('Sales Representative is not valid'));
        }
        return true;
    }
}
