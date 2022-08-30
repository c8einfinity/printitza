<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Model\ResourceModel;

use BrainActs\SalesRepresentative\Api\Data\MemberInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

/**
 * Class Member
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class Member extends AbstractDb
{
    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param Context $context
     * @param EntityManager $entityManager
     * @param MetadataPool $metadataPool
     * @param string $connectionName
     */
    public function __construct(
        Context $context,
        EntityManager $entityManager,
        MetadataPool $metadataPool,
        $connectionName = null
    ) {
    

        $this->entityManager = $entityManager;
        $this->metadataPool = $metadataPool;
        parent::__construct($context, $connectionName);
    }

    protected function _construct()
    {
        $this->_init('brainacts_salesrep_member', 'member_id');
    }

    /**
     * @inheritDoc
     */
    public function getConnection()
    {
        return $this->metadataPool->getMetadata(MemberInterface::class)->getEntityConnection();
    }

    /**
     * Get products ids to which specified item is assigned
     *
     * @param int $id
     * @return array
     * @throws LocalizedException
     * @throws \Exception
     */
    public function lookupProductIds($id)
    {
        $connection = $this->getConnection();

        $entityMetadata = $this->metadataPool->getMetadata(MemberInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $select = $connection->select()
            ->from(['product' => $this->getTable('brainacts_salesrep_member_product')], 'product_id')
            ->join(
                ['member' => $this->getMainTable()],
                'product.' . $linkField . ' = member.' . $linkField,
                []
            )
            ->where('member.' . $entityMetadata->getIdentifierField() . ' = :member_id');

        return $connection->fetchCol($select, ['member_id' => (int)$id]);
    }

    /**
     * Get customer ids to which specified item is assigned
     *
     * @param int $id
     * @return array
     * @throws LocalizedException
     * @throws \Exception
     */
    public function lookupCustomerIds($id)
    {
        $connection = $this->getConnection();

        $entityMetadata = $this->metadataPool->getMetadata(MemberInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $select = $connection->select()
            ->from(['customer' => $this->getTable('brainacts_salesrep_member_customer')], 'customer_id')
            ->join(
                ['member' => $this->getMainTable()],
                'customer.' . $linkField . ' = member.' . $linkField,
                []
            )
            ->where('member.' . $entityMetadata->getIdentifierField() . ' = :member_id');

        return $connection->fetchCol($select, ['member_id' => (int)$id]);
    }

    /**
     * Get Member IDs by customer ID
     * @param $customerId
     * @return array
     */
    public function getMembersByCustomer($customerId)
    {
        $connection = $this->getConnection();

        $select = $connection->select()
            ->from(['customer' => $this->getTable('brainacts_salesrep_member_customer')], 'member_id')
            ->where('customer.customer_id = :customer_id');

        return $connection->fetchCol($select, ['customer_id' => (int)$customerId]);
    }
    
    /**
     * @param $customerId
     * @param string $cols
     * @return array
     * @throws LocalizedException
     * @throws \Exception
     */
    public function getMembersNamesByCustomer($customerId, $cols = 'member_id')
    {
        $entityMetadata = $this->metadataPool->getMetadata(MemberInterface::class);

        $connection = $this->getConnection();

        $linkField = $entityMetadata->getLinkField();

        $select = $connection->select()
            ->from(['member' => $this->getMainTable()], $cols)
            ->join(
                ['customer' => $this->getTable('brainacts_salesrep_member_customer')],
                'customer.' . $linkField . ' = member.' . $linkField,
                []
            )
            ->where('customer.customer_id = :customer_id');

        return $connection->fetchAssoc($select, ['customer_id' => (int)$customerId]);
    }

    /**
     * @param AbstractModel $object
     * @return $this
     * @throws \Exception
     */
    public function save(AbstractModel $object)
    {
        $this->entityManager->save($object);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function delete(AbstractModel $object)
    {
        $this->entityManager->delete($object);
        return $this;
    }

    /**
     * Remove Members for special customers
     * @param $customerId
     * @return bool
     */
    public function removeMembersFromCustomer($customerId)
    {
        try {
            $connection = $this->getConnection();
            $table = $this->getTable('brainacts_salesrep_member_customer');
            $connection->delete($table, 'customer_id = ' . $customerId);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Assign customer to member
     * @param $customerId
     * @param $memberId
     * @return bool
     */
    public function applyMemberToCustomer($customerId, $memberId)
    {
        $connection = $this->getConnection();

        try {
            $table = $this->getTable('brainacts_salesrep_member_customer');

            $bind = [
                'customer_id' => $customerId,
                'member_id' => $memberId
            ];
            $connection->insert($table, $bind);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @return array
     * @throws LocalizedException
     * @throws \Exception
     */
    public function getMembersByCustomerId($id)
    {
        $connection = $this->getConnection();

        $entityMetadata = $this->metadataPool->getMetadata(MemberInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $select = $connection->select()
            ->from(['member' => $this->getMainTable()], ['firstname', 'lastname'])
            ->join(
                ['customer' => $this->getTable('brainacts_salesrep_member_customer')],
                'customer.' . $linkField . ' = member.' . $linkField,
                []
            )
            ->where('customer.customer_id = :customer_id');

        return $connection->fetchAssoc($select, ['customer_id' => (int)$id]);
    }

    /**
     * Load an object
     *
     * @param \BrainActs\SalesRepresentative\Model\Member|AbstractModel $object
     * @param mixed $value
     * @param string $field field to load by (defaults to model id)
     * @return $this
     * @throws LocalizedException
     * @throws \Exception
     */
    public function load(AbstractModel $object, $value, $field = null)
    {
        $memberId = $this->getMemberId($object, $value, $field);
        if ($memberId) {
            $this->entityManager->load($object, $memberId);
        }
        return $this;
    }

    /**
     * @param AbstractModel $object
     * @param mixed $value
     * @param null $field
     * @return bool|int|string
     * @throws LocalizedException
     * @throws \Exception
     */
    private function getMemberId(AbstractModel $object, $value, $field = null)
    {
        $entityMetadata = $this->metadataPool->getMetadata(MemberInterface::class);

        if (!$field) {
            $field = $entityMetadata->getIdentifierField();
        }
        $entityId = $value;
        if ($field != $entityMetadata->getIdentifierField() || $object->getStoreId()) {
            $select = $this->_getLoadSelect($field, $value, $object);
            $select->reset(Select::COLUMNS)
                ->columns($this->getMainTable() . '.' . $entityMetadata->getIdentifierField())
                ->limit(1);
            $result = $this->getConnection()->fetchCol($select);
            $entityId = count($result) ? $result[0] : false;
        }
        return $entityId;
    }

    /**
     * Create Confirmation Record
     * @param int $customerId
     * @param $newMembers
     * @param $currentMembersIds
     * @return bool
     */
    public function saveConfirmationRequest($customerId, $newMembers, $currentMembersIds)
    {

        $this->removeConfirmationRequest($customerId);

        //clear prev records
        $connection = $this->getConnection();

        try {
            $table = $this->getTable('brainacts_salesrep_confirm');

            $bind = [
                'customer_id' => $customerId,
                'from' => $currentMembersIds,
                'to' => $newMembers
            ];
            $connection->insert($table, $bind);
            return $connection->lastInsertId();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Remove confirmation request for customer
     * @param $customerId
     * @return bool
     */
    public function removeConfirmationRequest($customerId)
    {
        try {
            $connection = $this->getConnection();
            $table = $this->getTable('brainacts_salesrep_confirm');
            $connection->delete($table, 'customer_id = ' . $customerId);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Return count of requests for customer
     * @param $customerId
     * @return int
     */
    public function isExistRequest($customerId)
    {

        $connection = $this->getConnection();

        $select = $connection->select()
            ->from(['main_table' => $this->getTable('brainacts_salesrep_confirm')])
            ->where('main_table.customer_id = :customer_id');

        $result = $connection->fetchCol($select, ['customer_id' => (int)$customerId]);

        return count($result);
    }

    /**
     * Return confirmation request by id
     * @param $id
     * @return array
     */
    public function getRequestById($id)
    {
        $connection = $this->getConnection();

        $select = $connection->select()
            ->from(['main_table' => $this->getTable('brainacts_salesrep_confirm')])
            ->where('main_table.confirm_id = :confirm_id');

        $result = $connection->fetchRow($select, ['confirm_id' => (int)$id]);

        return $result;
    }
}
