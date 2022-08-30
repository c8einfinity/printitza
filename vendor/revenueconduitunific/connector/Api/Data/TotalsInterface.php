<?php

namespace Unific\Connector\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface TotalsInterface extends ExtensibleDataInterface
{
    /**
     * @return int
     */
    public function getCategory();

    /**
     * @param int $categoryCount
     * @return void
     */
    public function setCategory($categoryCount = 0);

    /**
     * @return int
     */
    public function getProduct();

    /**
     * @param int $productCount
     * @return void
     */
    public function setProduct($productCount = 0);

    /**
     * @return int
     */
    public function getOrder();

    /**
     * @param int $orderCount
     * @return void
     */
    public function setOrder($orderCount = 0);

    /**
     * @return int
     */
    public function getCustomer();

    /**
     * @param int $customerCount
     * @return void
     */
    public function setCustomer($customerCount = 0);
}
