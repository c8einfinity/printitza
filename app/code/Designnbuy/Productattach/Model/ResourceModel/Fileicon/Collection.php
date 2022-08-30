<?php
namespace Designnbuy\Productattach\Model\ResourceModel\Fileicon;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Designnbuy\Productattach\Model\Fileicon',
            'Designnbuy\Productattach\Model\ResourceModel\Fileicon'
        );
    }
}
