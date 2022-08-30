<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace BrainActs\SalesRepresentative\Model\ResourceModel\Member;

use BrainActs\SalesRepresentative\Api\Data\MemberInterface;
use BrainActs\SalesRepresentative\Model\ResourceModel\AbstractCollection;

/**
 * Class Collection
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'member_id';//@codingStandardsIgnoreLine

    /**
     * Perform operations after collection load
     * @return $this
     * @throws \Exception
     */
    protected function _afterLoad()//@codingStandardsIgnoreLine
    {
        $entityMetadata = $this->metadataPool->getMetadata(MemberInterface::class);

        $this->performAfterLoad('brainacts_salesrep_member_customer', $entityMetadata->getLinkField());

        return parent::_afterLoad();//@codingStandardsIgnoreLine
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \BrainActs\SalesRepresentative\Model\Member::class,
            \BrainActs\SalesRepresentative\Model\ResourceModel\Member::class
        );

        $this->_map['fields']['customer'] = 'customer_table.customer_id';
        $this->_map['fields']['product'] = 'product_table.product_id';
    }

    /**
     * Returns pairs block_id - title
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('member_id', 'title');
    }

    /**
     * Add filter by customer
     *
     * @param $customerId
     * @return $this
     */
    public function addCustomerFilter($customerId)
    {
        $this->performAddCustomerFilter($customerId);

        return $this;
    }

    /**
     * Add filter by product
     *
     * @param $productId
     * @return $this
     */
    public function addProductFilter($productId)
    {
        $this->performAddProductFilter($productId);

        return $this;
    }

    /**
     * Join customer/product relation table if there is customer/product filter
     *
     * @return void
     * @throws \Exception
     */
    protected function _renderFiltersBefore()//@codingStandardsIgnoreLine
    {
        $entityMetadata = $this->metadataPool->getMetadata(MemberInterface::class);
        $this->joinCustomerRelationTable('brainacts_salesrep_member_customer', $entityMetadata->getLinkField());
        $this->joinProductRelationTable('brainacts_salesrep_member_product', $entityMetadata->getLinkField());
    }
}
