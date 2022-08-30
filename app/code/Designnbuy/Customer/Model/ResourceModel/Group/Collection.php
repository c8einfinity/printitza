<?php
namespace Designnbuy\Customer\Model\ResourceModel\Group;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
	{
		$this->_init('Designnbuy\Customer\Model\Group', 'Designnbuy\Customer\Model\ResourceModel\Group');
        $this->_map['fields']['page_id'] = 'main_table.page_id';
	}

}	