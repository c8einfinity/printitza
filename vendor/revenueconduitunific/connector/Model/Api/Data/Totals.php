<?php

namespace Unific\Connector\Model\Api\Data;

class Totals implements \Unific\Connector\Api\Data\TotalsInterface
{
    protected $category;
    protected $product;
    protected $customer;
    protected $order;

    /**
     * @return int
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param int $category
     */
    public function setCategory($category = 0)
    {
        $this->category = $category;
    }

    /**
     * @return int
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param int $product
     */
    public function setProduct($product = 0)
    {
        $this->product = $product;
    }

    /**
     * @return int
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * @param int $customer
     */
    public function setCustomer($customer = 0)
    {
        $this->customer = $customer;
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param int $order
     */
    public function setOrder($order = 0)
    {
        $this->order = $order;
    }
}
