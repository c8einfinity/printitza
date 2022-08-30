<?php

namespace Designnbuy\Designidea\Api\Data\Category;

/**
 * @api
 */
interface CategorySearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get designideas list.
     *
     * @return \Designnbuy\Designidea\Api\Data\Category\CategoryInterface[]
     */
    public function getItems();

    /**
     * Set designideas list.
     *
     * @param \Designnbuy\Designidea\Api\Data\Category\CategoryInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
