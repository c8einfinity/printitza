<?php
namespace Designnbuy\Reseller\Model\ResourceModel;

class Products extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('designnbuy_reseller_productpool_products', 'productpool_id');
    }
}
?>