<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;
use BrainActs\SalesRepresentative\Api\MemberRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

/**
 * Class User
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class Member implements OptionSourceInterface
{

    /**
     * @var MemberRepositoryInterface
     */
    private $memberRepositoryInterface;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var array
     */
    private $options;

    /**
     * Member constructor.
     * @param MemberRepositoryInterface $MemberRepositoryInterface
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        MemberRepositoryInterface $MemberRepositoryInterface,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->memberRepositoryInterface = $MemberRepositoryInterface;
    }

    /**
     * Get options
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function toOptionArray()
    {
        if ($this->options !== null) {
            return $this->options;
        }

        $searchCriteria = $this->searchCriteriaBuilder
//            ->addFilter(
//                \BrainActs\SalesRepresentative\Model\Member::IS_ACTIVE,
//                \BrainActs\SalesRepresentative\Model\Member::STATUS_ENABLED,
//                'eq'
//            )
            ->create();
        $collection = $this->memberRepositoryInterface->getList($searchCriteria)->getItems();

        $options = [];
        /** @var \BrainActs\SalesRepresentative\Model\Member $member */

        foreach ($collection as $member) {
            $options[] = [
                'label' => implode(', ', [$member['firstname'], $member['lastname']]),
                'value' => $member['member_id'],
            ];
        }

        $this->options = $options;

        return $this->options;
    }
}
