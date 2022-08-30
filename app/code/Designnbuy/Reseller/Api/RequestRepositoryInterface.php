<?php
/**
 * Designnbuy_Reseller extension
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category  Designnbuy
 * @package   Designnbuy_Reseller
 * @copyright Copyright (c) 2018
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 */
namespace Designnbuy\Reseller\Api;

/**
 * @api
 */
interface RequestRepositoryInterface
{
    /**
     * Save Request.
     *
     * @param \Designnbuy\Reseller\Api\Data\RequestInterface $request
     * @return \Designnbuy\Reseller\Api\Data\RequestInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Designnbuy\Reseller\Api\Data\RequestInterface $request);

    /**
     * Retrieve Request
     *
     * @param int $requestId
     * @return \Designnbuy\Reseller\Api\Data\RequestInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($requestId);

    /**
     * Retrieve Requests matching the specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Designnbuy\Reseller\Api\Data\RequestSearchResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete Request.
     *
     * @param \Designnbuy\Reseller\Api\Data\RequestInterface $request
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Designnbuy\Reseller\Api\Data\RequestInterface $request);

    /**
     * Delete Request by ID.
     *
     * @param int $requestId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($requestId);
}
