<?php

namespace Unific\Connector\Model\Audit;

use Magento\Framework\Model\AbstractModel;

class Log extends AbstractModel
{
    /**
     * Initialize resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Unific\Connector\Model\ResourceModel\Audit\Log::class);
    }
}
