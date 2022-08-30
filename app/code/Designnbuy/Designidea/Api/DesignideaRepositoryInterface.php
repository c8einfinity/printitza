<?php

namespace Designnbuy\Designidea\Api;

/**
 * @api
 */
interface DesignideaRepositoryInterface
{
    /**
     * Create designidea
     *
     * @param \Designnbuy\Designidea\Api\Data\DesignideaInterface $designidea
     * @return \Designnbuy\Designidea\Api\Data\DesignideaInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Designnbuy\Designidea\Api\Data\DesignideaInterface $designidea);

    /**
     * Get info about designidea by designidea id
     *
     * @param string $sku
     * @param int|null $storeId
     * @return \Designnbuy\Designidea\Api\Data\DesignideaInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($designideaId, $storeId = null);

    /**
     * Delete designidea
     *
     * @param \Designnbuy\Designidea\Api\Data\DesignideaInterface $designidea
     * @return bool Will returned True if deleted
     * @throws \Magento\Framework\Exception\StateException
     */
    public function delete(\Designnbuy\Designidea\Api\Data\DesignideaInterface $designidea);

    /**
     * @param string $id
     * @return bool Will returned True if deleted
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function deleteById($id);

    /**
     * Get designidea list
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Designnbuy\Designidea\Api\Data\DesignideaSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}