<?php

namespace Unific\Connector\Model\ResourceModel\Queue;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'guid';

    /**
     * Define resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Unific\Connector\Model\Queue::class,
            \Unific\Connector\Model\ResourceModel\Queue::class
        );
    }
}
