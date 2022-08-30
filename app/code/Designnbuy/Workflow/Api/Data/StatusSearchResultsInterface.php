<?php


namespace Designnbuy\Workflow\Api\Data;

interface StatusSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{


    /**
     * Get Status list.
     * @return \Designnbuy\Workflow\Api\Data\StatusInterface[]
     */
    
    public function getItems();

    /**
     * Set Title list.
     * @param \Designnbuy\Workflow\Api\Data\StatusInterface[] $items
     * @return $this
     */
    
    public function setItems(array $items);
}
