<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace BrainActs\SalesRepresentative\Model\ResourceModel\Member\Relation\Product;

use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use BrainActs\SalesRepresentative\Api\Data\MemberInterface;
use BrainActs\SalesRepresentative\Model\ResourceModel\Member;
use Magento\Framework\EntityManager\MetadataPool;

/**
 * Class SaveHandler
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class SaveHandler implements ExtensionInterface
{
    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var Member
     */
    private $resourceMember;

    /**
     * @param MetadataPool $metadataPool
     * @param Member $resourceMember
     */
    public function __construct(
        MetadataPool $metadataPool,
        Member $resourceMember
    ) {
    
        $this->metadataPool = $metadataPool;
        $this->resourceMember = $resourceMember;
    }

    /**
     * @param object $entity
     * @param array $arguments
     * @return object
     * @throws \Exception
     */
    public function execute($entity, $arguments = [])
    {

        $this->saveProducts($entity);
        $this->saveCustomers($entity);

        return $entity;
    }

    /**
     * Update related products
     *
     * @param $entity
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function saveProducts($entity)
    {
        $entityMetadata = $this->metadataPool->getMetadata(MemberInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $connection = $entityMetadata->getEntityConnection();

        $oldProducts = $this->resourceMember->lookupProductIds((int)$entity->getId());

        $newProductsJson = $entity->getMemberProducts();
        if ($newProductsJson === null) {
            return;
        }
        $list = json_decode($newProductsJson, true);

        if (is_array($list)) {
            $newProducts = array_keys($list);
        } else {
            $newProducts = [];
        }

        $table = $this->resourceMember->getTable('brainacts_salesrep_member_product');

        $delete = array_diff($oldProducts, $newProducts);
        if ($delete) {
            $where = [
                $linkField . ' = ?' => (int)$entity->getData($linkField),
                'product_id IN (?)' => $delete,
            ];
            $connection->delete($table, $where);
        }

        $insert = array_diff($newProducts, $oldProducts);
        if ($insert) {
            $data = [];
            foreach ($insert as $productId) {
                $data[] = [
                    $linkField => (int)$entity->getData($linkField),
                    'product_id' => (int)$productId,
                ];
            }
            $connection->insertMultiple($table, $data);
        }
    }

    /**
     * Update related customers
     *
     * @param $entity
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function saveCustomers($entity)
    {
        $entityMetadata = $this->metadataPool->getMetadata(MemberInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $connection = $entityMetadata->getEntityConnection();

        $oldCustomers = $this->resourceMember->lookupCustomerIds((int)$entity->getId());

        $newCustomersJson = $entity->getMemberCustomers();
        if ($newCustomersJson === null) {
            return;
        }
        $list = json_decode($newCustomersJson, true);

        if (is_array($list)) {
            $newCustomers = array_keys($list);
        } else {
            $newCustomers = [];
        }

        $table = $this->resourceMember->getTable('brainacts_salesrep_member_customer');

        $delete = array_diff($oldCustomers, $newCustomers);
        if ($delete) {
            $where = [
                $linkField . ' = ?' => (int)$entity->getData($linkField),
                'customer_id IN (?)' => $delete,
            ];
            $connection->delete($table, $where);
        }

        $insert = array_diff($newCustomers, $oldCustomers);
        if ($insert) {
            $data = [];
            foreach ($insert as $customerId) {
                $data[] = [
                    $linkField => (int)$entity->getData($linkField),
                    'customer_id' => (int)$customerId,
                ];
            }
            $connection->insertMultiple($table, $data);
        }
    }
}
