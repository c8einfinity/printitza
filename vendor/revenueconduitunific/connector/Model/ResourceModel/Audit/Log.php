<?php

namespace Unific\Connector\Model\ResourceModel\Audit;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Log extends AbstractDb
{
    /**
     * Initialize resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init('unific_connector_audit_log', 'id');
    }
}
