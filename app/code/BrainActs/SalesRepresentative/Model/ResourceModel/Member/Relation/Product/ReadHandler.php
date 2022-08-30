<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace BrainActs\SalesRepresentative\Model\ResourceModel\Member\Relation\Product;

use BrainActs\SalesRepresentative\Model\ResourceModel\Member;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

/**
 * Class ReadHandler
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class ReadHandler implements ExtensionInterface
{
    /**
     * @var Member
     */
    private $resourceMember;

    /**
     * @param Member $resourceMember
     */
    public function __construct(
        Member $resourceMember
    ) {
    
        $this->resourceMember = $resourceMember;
    }

    /**
     * @param object $entity
     * @param array $arguments
     * @return object
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute($entity, $arguments = [])
    {

        if ($entity->getId()) {
            //products
            $products = $this->resourceMember->lookupProductIds((int)$entity->getId());
            $newListProducts = [];
            if (count($products)) {
                foreach ($products as $product) {
                    $newListProducts[$product] = '';
                }
            }
            $entity->setData('product_id', $newListProducts);
            $entity->setData('products', $newListProducts);

            //customers
            $newListCustomers = [];
            $customers = $this->resourceMember->lookupCustomerIds((int)$entity->getId());
            if (count($customers)) {
                foreach ($customers as $customer) {
                    $newListCustomers[$customer] = '';
                }
            }
            $entity->setData('customer_id', $newListCustomers);
            $entity->setData('customers', $newListCustomers);
        }
        return $entity;
    }
}
