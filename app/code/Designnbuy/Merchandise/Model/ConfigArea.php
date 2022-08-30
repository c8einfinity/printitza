<?php
namespace Designnbuy\Merchandise\Model;
class ConfigArea extends \Magento\Framework\Model\AbstractModel
{

    protected $_eventPrefix = 'designnbuy_merchandise_configarea';

    protected function _construct()
    {
        $this->_init('Designnbuy\Merchandise\Model\ResourceModel\ConfigArea');
    }
}