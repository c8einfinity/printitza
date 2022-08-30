<?php
namespace Designnbuy\Reseller\Model\ResourceModel\Resellers;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Designnbuy\Reseller\Model\Resellers', 'Designnbuy\Reseller\Model\ResourceModel\Resellers');
        $this->_map['fields']['reseller_id'] = 'main_table.reseller_id';
    }

}
?>