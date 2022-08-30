<?php
namespace Designnbuy\Workflow\Model\ResourceModel\Status;

use Designnbuy\Workflow\Model\ResourceModel\StatusAbstractCollection;

class Collection extends StatusAbstractCollection
{
    protected $_idFieldName = 'status_id';
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Designnbuy\Workflow\Model\Status',
            'Designnbuy\Workflow\Model\ResourceModel\Status'
        );
    }

    /**
     * Convert collection items to select options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('status_id', 'title');
    }
}
