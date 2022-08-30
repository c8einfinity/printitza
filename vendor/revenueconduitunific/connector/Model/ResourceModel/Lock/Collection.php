<?php

namespace Unific\Connector\Model\ResourceModel\Lock;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Define resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Unific\Connector\Model\Lock::class,
            \Unific\Connector\Model\ResourceModel\Lock::class
        );
    }
}
