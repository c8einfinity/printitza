<?php

namespace Unific\Connector\Model;

use Magento\Framework\Model\AbstractModel;

class Historical extends AbstractModel
{
    /**
     * Initialize resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Historical::class);
    }
}
