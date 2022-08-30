<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

/**
 * Customer problems collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Designnbuy\Customer\Model\ResourceModel\Grid;

class Collection extends \Designnbuy\Customer\Model\ResourceModel\Problem\Collection
{
    /**
     * Adds queue info to grid
     *
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|\Designnbuy\Customer\Model\ResourceModel\Grid\Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addDesignInfo()->addQueueInfo();
        return $this;
    }
}
