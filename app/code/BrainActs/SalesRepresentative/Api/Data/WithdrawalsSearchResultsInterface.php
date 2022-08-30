<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace BrainActs\SalesRepresentative\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Interface for withdrawals search results.
 * @api
 */
interface WithdrawalsSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get member list.
     *
     * @return \BrainActs\SalesRepresentative\Api\Data\WithdrawalsInterface[]
     */
    public function getItems();

    /**
     * Set member list.
     *
     * @param \BrainActs\SalesRepresentative\Api\Data\WithdrawalsInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
