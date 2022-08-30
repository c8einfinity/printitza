<?php

namespace Designnbuy\Template\Api\Category;

/**
 * @api
 */
interface CategoryRepositoryInterface
{
    /**
     * Create template
     *
     * @param \Designnbuy\Template\Api\Data\TemplateInterface $template
     * @return \Designnbuy\Template\Api\Data\TemplateInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Designnbuy\Template\Api\Data\Category\CategoryInterface $template);

    /**
     * Get info about template by template id
     *
     * @param string $sku
     * @param int|null $storeId
     * @return \Designnbuy\Template\Api\Data\TemplateInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($templateId, $storeId = null);

    /**
     * Delete template
     *
     * @param \Designnbuy\Template\Api\Data\TemplateInterface $template
     * @return bool Will returned True if deleted
     * @throws \Magento\Framework\Exception\StateException
     */
    public function delete(\Designnbuy\Template\Api\Data\Category\CategoryInterface $template);

    /**
     * @param string $id
     * @return bool Will returned True if deleted
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function deleteById($id);

    /**
     * Get template list
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Designnbuy\Template\Api\Data\TemplateSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}