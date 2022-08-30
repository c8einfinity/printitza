<?php

namespace Designnbuy\Template\Api\Data\Category;

/**
 * @api
 */
interface CategorySearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get templates list.
     *
     * @return \Designnbuy\Template\Api\Data\Category\CategoryInterface[]
     */
    public function getItems();

    /**
     * Set templates list.
     *
     * @param \Designnbuy\Template\Api\Data\Category\CategoryInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
