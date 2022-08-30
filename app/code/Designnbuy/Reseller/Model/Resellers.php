<?php
namespace Designnbuy\Reseller\Model;

class Resellers extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Designnbuy\Reseller\Model\ResourceModel\Resellers');
    }
}
?>