<?php


namespace Designnbuy\Vendor\Api\Data;

interface UserSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{


    /**
     * Get User list.
     * @return \Designnbuy\Vendor\Api\Data\UserInterface[]
     */
    
    public function getItems();

    /**
     * Set Title list.
     * @param \Designnbuy\Vendor\Api\Data\UserInterface[] $items
     * @return $this
     */
    
    public function setItems(array $items);
}
