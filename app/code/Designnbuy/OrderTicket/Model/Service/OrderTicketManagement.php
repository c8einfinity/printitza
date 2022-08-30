<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Model\Service;

use Designnbuy\OrderTicket\Model\OrderTicket;
use Magento\Framework\Api\FilterBuilder;
use Designnbuy\OrderTicket\Api\OrderTicketRepositoryInterface;
use Designnbuy\OrderTicket\Api\OrderTicketManagementInterface;
use Designnbuy\OrderTicket\Model\OrderTicket\PermissionChecker;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Authorization\Model\UserContextInterface;

/**
 * Class OrderTicketManagement
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class OrderTicketManagement implements OrderTicketManagementInterface
{
    /**
     * Permission checker
     *
     * @var PermissionChecker
     */
    protected $permissionChecker;

    /**
     * OrderTicket repository
     *
     * @var OrderTicketRepositoryInterface
     */
    protected $orderticketRepository;

    /**
     * User context
     *
     * @var UserContextInterface
     */
    protected $userContext;

    /**
     * Filter builder
     *
     * @var FilterBuilder
     */
    protected $filterBuilder;

    /**
     * Search criteria builder
     *
     * @var SearchCriteriaBuilder
     */
    protected $criteriaBuilder;

    /**
     * Constructor
     *
     * @param PermissionChecker $permissionChecker
     * @param OrderTicketRepositoryInterface $orderticketRepository
     * @param UserContextInterface $userContext
     * @param FilterBuilder $filterBuilder
     * @param SearchCriteriaBuilder $criteriaBuilder
     */
    public function __construct(
        PermissionChecker $permissionChecker,
        OrderTicketRepositoryInterface $orderticketRepository,
        UserContextInterface $userContext,
        FilterBuilder $filterBuilder,
        SearchCriteriaBuilder $criteriaBuilder
    ) {
        $this->permissionChecker = $permissionChecker;
        $this->orderticketRepository = $orderticketRepository;
        $this->userContext = $userContext;
        $this->filterBuilder = $filterBuilder;
        $this->criteriaBuilder = $criteriaBuilder;
    }

    /**
     * Save ORDERTICKET
     *
     * @param \Designnbuy\OrderTicket\Api\Data\OrderTicketInterface $orderticketDataObject
     * @return \Designnbuy\OrderTicket\Api\Data\OrderTicketInterface
     */
    public function saveOrderTicket(\Designnbuy\OrderTicket\Api\Data\OrderTicketInterface $orderticketDataObject)
    {
        $this->permissionChecker->checkOrderTicketForCustomerContext();
        return $this->orderticketRepository->save($orderticketDataObject);
    }

    /**
     * Return list of orderticket data objects based on search criteria
     *
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria
     * @return \Designnbuy\OrderTicket\Api\Data\OrderTicketSearchResultInterface
     */
    public function search(\Magento\Framework\Api\SearchCriteria $searchCriteria)
    {
        $this->permissionChecker->checkOrderTicketForCustomerContext();
        foreach ($searchCriteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $this->criteriaBuilder->addFilters(
                    [
                        $filter->getConditionType() => $filter,
                    ]
                );
            }
        }
        $filter = $this->filterBuilder->setField(OrderTicket::CUSTOMER_ID)->setValue($this->userContext->getUserId())->create();
        $this->criteriaBuilder->addFilters(
            [
                'eq' => $filter,
            ]
        );

        return $this->orderticketRepository->getList($this->criteriaBuilder->create());
    }
}
