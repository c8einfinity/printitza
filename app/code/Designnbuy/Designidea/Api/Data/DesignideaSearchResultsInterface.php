<?php

namespace Designnbuy\Designidea\Api\Data;

/**
 * @api
 */
interface DesignideaSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get designideas list.
     *
     * @return \Designnbuy\Designidea\Api\Data\DesignideaInterface[]
     */
    public function getItems();

    /**
     * Set designideas list.
     *
     * @param \Designnbuy\Designidea\Api\Data\DesignideaInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
