<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Customer\Model\ResourceModel;

/**
 * Customer problem resource model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Problem extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Define main table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('customer_problem', 'problem_id');
    }
}
