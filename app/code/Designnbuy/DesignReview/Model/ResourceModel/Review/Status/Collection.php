<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Review statuses collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Designnbuy\DesignReview\Model\ResourceModel\Review\Status;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Review status table
     *
     * @var string
     */
    protected $_reviewStatusTable;

    /**
     * Collection model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Designnbuy\DesignReview\Model\Review\Status::class,
            \Designnbuy\DesignReview\Model\ResourceModel\Review\Status::class
        );
    }

    /**
     * Convert items array to array for select options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return parent::_toOptionArray('status_id', 'status_code');
    }
}
