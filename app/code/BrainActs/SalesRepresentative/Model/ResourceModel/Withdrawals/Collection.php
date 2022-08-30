<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Model\ResourceModel\Withdrawals;

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
    protected $_idFieldName = 'withdrawal_id';//@codingStandardsIgnoreLine

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()//@codingStandardsIgnoreLine
    {
        $this->_init(
            \BrainActs\SalesRepresentative\Model\Withdrawals::class,
            \BrainActs\SalesRepresentative\Model\ResourceModel\Withdrawals::class
        );
    }

    /**
     * Add filter by customer
     *
     * @param $customerId
     * @return $this
     */
    public function addCustomerFilter($customerId)
    {
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
        return $this;
    }
}
