<?php
/**
 * Customer design grid collection
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Customer\Model\ResourceModel\Design\Grid;

class Collection extends \Designnbuy\Customer\Model\ResourceModel\Design\Collection
{
    /**
     * Sets flag for customer info loading on load
     *
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->showCustomerInfo(true)->addDesignTypeField()->showStoreInfo();
        return $this;
    }
}
