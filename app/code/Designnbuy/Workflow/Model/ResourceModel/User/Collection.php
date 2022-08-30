<?php


namespace Designnbuy\Workflow\Model\ResourceModel\User;

use Designnbuy\Workflow\Model\ResourceModel\UserAbstractCollection;

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
            'Designnbuy\Workflow\Model\User',
            'Designnbuy\Workflow\Model\ResourceModel\User'
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

    protected function _renderFiltersBefore()
    {
        $wherePart = $this->getSelect()->getPart(\Magento\Framework\DB\Select::WHERE);
        foreach ($wherePart as $key => $cond) {
            if (strpos($cond, '`username`') !== false) {
                $wherePart[$key] = str_replace('`username`', '`admin_user1`.`username`', $cond);
            }
            if (strpos($cond, '`firstname`') !== false) {
                $wherePart[$key] = str_replace('`firstname`', '`admin_user1`.`firstname`', $cond);
            }
            if (strpos($cond, '`lastname`') !== false) {
                $wherePart[$key] = str_replace('`lastname`', '`admin_user1`.`lastname`', $cond);
            }
            if (strpos($cond, '`email`') !== false) {
                $wherePart[$key] = str_replace('`email`', '`admin_user1`.`email`', $cond);
            }
        }
        $this->getSelect()->setPart(\Magento\Framework\DB\Select::WHERE, $wherePart);
        parent::_renderFiltersBefore();
    }

    public function addAdminUser()
    {
        $this->getSelect()->joinLeft(
            ['admin_user' => $this->getTable('admin_user')],
            'main_table.user_id = admin_user.user_id',
            ['username','firstname','lastname','email','is_active']
        );

        return $this;
    }
}
