<?php

namespace Designnbuy\Vendor\Model\ResourceModel\Transaction;

use Designnbuy\Vendor\Model\ResourceModel\TransactionAbstractCollection;

class Collection extends TransactionAbstractCollection
{
    protected $_idFieldName = 'transaction_id';
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Designnbuy\Vendor\Model\Transaction',
            'Designnbuy\Vendor\Model\ResourceModel\Transaction'
        );
        $this->_map['fields']['transaction_id'] = 'main_table.transaction_id';
        $this->_map['fields']['vendor_id'] = 'main_table.vendor_id';
    }

    /**
     * Convert collection items to select options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('transaction_id', 'title');
    }

    
}
