<?php


namespace Designnbuy\Workflow\Api\Data;

interface RoleSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{


    /**
     * Get Role list.
     * @return \Designnbuy\Workflow\Api\Data\RoleInterface[]
     */
    
    public function getItems();

    /**
     * Set Title list.
     * @param \Designnbuy\Workflow\Api\Data\RoleInterface[] $items
     * @return $this
     */
    
    public function setItems(array $items);
}
