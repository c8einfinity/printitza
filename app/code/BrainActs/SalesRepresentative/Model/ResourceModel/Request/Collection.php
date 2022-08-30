<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace BrainActs\SalesRepresentative\Model\ResourceModel\Request;

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
    protected $_idFieldName = 'member_id';

    /**
     * Perform operations after collection load
     * @return $this
     * @throws \Exception
     */
    protected function _afterLoad()
    {
        //$entityMetadata = $this->metadataPool->getMetadata(MemberInterface::class);

        //$this->performAfterLoad('brainacts_salesrep_member_customer', $entityMetadata->getLinkField());

        return parent::_afterLoad();
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'BrainActs\SalesRepresentative\Model\Member',
            'BrainActs\SalesRepresentative\Model\ResourceModel\Member'
        );

        $this->_map['fields']['customer'] = 'customer_table.customer_id';
    }

    /**
     * Returns pairs block_id - title
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('block_id', 'title');
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
     */
    protected function _renderFiltersBefore()
    {
        //$this->joinCustomerRequestRelationTable('customer_entity', 'customer_id');
        parent::_renderFiltersBefore();
    }
}
