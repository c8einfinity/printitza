<?php


namespace Designnbuy\Vendor\Api\Data;

interface TransactionSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{


    /**
     * Get Transaction list.
     * @return \Designnbuy\Vendor\Api\Data\TransactionInterface[]
     */
    
    public function getItems();

    /**
     * Set Title list.
     * @param \Designnbuy\Vendor\Api\Data\TransactionInterface[] $items
     * @return $this
     */
    
    public function setItems(array $items);
}
