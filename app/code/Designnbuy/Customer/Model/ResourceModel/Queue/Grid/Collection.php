<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Customer queue data grid collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Designnbuy\Customer\Model\ResourceModel\Queue\Grid;

class Collection extends \Designnbuy\Customer\Model\ResourceModel\Queue\Collection
{
    /**
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addDesignsInfo();
        return $this;
    }
}
