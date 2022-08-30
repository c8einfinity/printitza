<?php


namespace Designnbuy\Workflow\Api\Data;

interface UserSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{


    /**
     * Get User list.
     * @return \Designnbuy\Workflow\Api\Data\UserInterface[]
     */
    
    public function getItems();

    /**
     * Set Title list.
     * @param \Designnbuy\Workflow\Api\Data\UserInterface[] $items
     * @return $this
     */
    
    public function setItems(array $items);
}
