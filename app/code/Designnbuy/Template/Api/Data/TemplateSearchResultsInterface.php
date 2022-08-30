<?php

namespace Designnbuy\Template\Api\Data;

/**
 * @api
 */
interface TemplateSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get templates list.
     *
     * @return \Designnbuy\Template\Api\Data\TemplateInterface[]
     */
    public function getItems();

    /**
     * Set templates list.
     *
     * @param \Designnbuy\Template\Api\Data\TemplateInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
