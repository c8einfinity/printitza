<?php

namespace Designnbuy\Customer\Model;

use Magento\Framework\Model\AbstractModel;


class Group extends AbstractModel
{
   protected function _construct()
	{
		$this->_init('Designnbuy\Customer\Model\ResourceModel\Group');
	}
    
}