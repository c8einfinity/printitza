<?php


namespace Designnbuy\Workflow\Model\ResourceModel\Role;

use Designnbuy\Workflow\Model\ResourceModel\RoleAbstractCollection;

class Collection extends RoleAbstractCollection
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
            'Designnbuy\Workflow\Model\Role',
            'Designnbuy\Workflow\Model\ResourceModel\Role'
        );
    }

    /**
     * Convert collection items to select options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('id', 'title');
    }
}
