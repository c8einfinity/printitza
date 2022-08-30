<?php


namespace Designnbuy\Workflow\Api\Data;

interface GroupSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{


    /**
     * Get Group list.
     * @return \Designnbuy\Workflow\Api\Data\GroupInterface[]
     */
    
    public function getItems();

    /**
     * Set Title list.
     * @param \Designnbuy\Workflow\Api\Data\GroupInterface[] $items
     * @return $this
     */
    
    public function setItems(array $items);
}
