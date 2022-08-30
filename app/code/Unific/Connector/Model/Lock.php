<?php

namespace Unific\Connector\Model;

use Magento\Framework\Model\AbstractModel;

class Lock extends AbstractModel
{
    /**
     * Initialize resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Unific\Connector\Model\ResourceModel\Lock::class);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->_getData('type');
    }

    /**
     * @param string $type
     * @return void
     */
    public function setType($type)
    {
        $this->setData('type', $type);
    }
}
