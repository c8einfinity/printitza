<?php


namespace Designnbuy\Vendor\Model\ResourceModel\User;

use Designnbuy\Vendor\Model\ResourceModel\UserAbstractCollection;

class Collection extends UserAbstractCollection
{
    protected $_idFieldName = 'id';
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Designnbuy\Vendor\Model\User',
            'Designnbuy\Vendor\Model\ResourceModel\User'
        );
    }

    protected function _initSelect()
    {
        parent::_initSelect();

        $this->getSelect()->joinLeft(
            ['admin_user1' => $this->getTable('admin_user')],
            'main_table.user_id = admin_user1.user_id',
            ['username','firstname','lastname','email']
        );

        //return $this;
    }

    public function addAdminUser()
    {
        $this->getSelect()->joinLeft(
            ['admin_user' => $this->getTable('admin_user')],
            'main_table.user_id = admin_user.user_id',
            ['username','firstname','lastname','email']
        );

        return $this;
    }
}
