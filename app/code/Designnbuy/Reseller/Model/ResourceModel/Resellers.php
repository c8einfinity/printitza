<?php
namespace Designnbuy\Reseller\Model\ResourceModel;

class Resellers extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('designnbuy_resellers', 'reseller_id');
    }
}
?>