<?php

namespace Designnbuy\Workflow\Model\ResourceModel\Group;

use Designnbuy\Workflow\Model\ResourceModel\GroupAbstractCollection;

class Collection extends GroupAbstractCollection
{
    protected $_idFieldName = 'group_id';
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Designnbuy\Workflow\Model\Group',
            'Designnbuy\Workflow\Model\ResourceModel\Group'
        );
        $this->_map['fields']['group_id'] = 'main_table.group_id';
    }

    /**
     * Convert collection items to select options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('group_id', 'title');
    }
}
