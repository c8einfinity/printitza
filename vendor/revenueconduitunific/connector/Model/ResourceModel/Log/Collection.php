<?php

namespace Unific\Connector\Model\ResourceModel\Log;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';
    /**
     * Define resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Unific\Connector\Model\Audit\Log::class,
            \Unific\Connector\Model\ResourceModel\Audit\Log::class
        );
    }
}
