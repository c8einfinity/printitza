<?php
namespace Designnbuy\Productattach\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface FileiconRepositoryInterface
{


    /**
     * Save Fileicon
     * @param \Designnbuy\Productattach\Api\Data\FileiconInterface $fileicon
     * @return \Designnbuy\Productattach\Api\Data\FileiconInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Designnbuy\Productattach\Api\Data\FileiconInterface $fileicon
    );

    /**
     * Retrieve Fileicon
     * @param string $fileiconId
     * @return \Designnbuy\Productattach\Api\Data\FileiconInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($fileiconId);

    /**
     * Retrieve Fileicon matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Designnbuy\Productattach\Api\Data\FileiconSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete Fileicon
     * @param \Designnbuy\Productattach\Api\Data\FileiconInterface $fileicon
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Designnbuy\Productattach\Api\Data\FileiconInterface $fileicon
    );

    /**
     * Delete Fileicon by ID
     * @param string $fileiconId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($fileiconId);
}
