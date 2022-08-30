<?php
namespace Designnbuy\Reseller\Model\ResourceModel\Products;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Designnbuy\Reseller\Model\Products', 'Designnbuy\Reseller\Model\ResourceModel\Products');
        $this->_map['fields']['productpool_id'] = 'main_table.productpool_id';
    }

    public function addFieldToFilter($field, $condition = null)
    {
        return parent::addFieldToFilter($field, $condition);
    }
}
?>