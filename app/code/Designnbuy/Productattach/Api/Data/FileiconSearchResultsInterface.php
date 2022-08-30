<?php
namespace Designnbuy\Productattach\Api\Data;

interface FileiconSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get productattach list.
     * @return \Designnbuy\Productattach\Api\Data\FileiconInterface[]
     */
    public function getItems();

    /**
     * Set test list.
     * @param \Designnbuy\Productattach\Api\Data\FileiconInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}