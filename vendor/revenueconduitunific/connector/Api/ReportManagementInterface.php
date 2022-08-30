<?php

namespace Unific\Connector\Api;

interface ReportManagementInterface
{
    /**
     * Get the total amount of orders
     *
     * @api
     * @return string
     */
    public function getOrderCount();

    /**
     * Get the total amount of customers
     *
     * @api
     * @return string
     */
    public function getCustomerCount();

    /**
     * Get the total amount of categories
     *
     * @api
     * @return string
     */
    public function getCategoryCount();

    /**
     * Get the total amount of products
     *
     * @api
     * @return string
     */
    public function getProductCount();
}
