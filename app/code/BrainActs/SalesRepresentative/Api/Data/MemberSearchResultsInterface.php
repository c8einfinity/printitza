<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace BrainActs\SalesRepresentative\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for member search results.
 * @api
 */
interface MemberSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get member list.
     *
     * @return \BrainActs\SalesRepresentative\Api\Data\MemberInterface[]
     */
    public function getItems();

    /**
     * Set member list.
     *
     * @param \BrainActs\SalesRepresentative\Api\Data\MemberInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
