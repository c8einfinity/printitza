<?php

namespace Designnbuy\Customer\Model\ResourceModel\Image;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Contact Resource Model Collection
 *
 * @author      Pierre FAY
 */
class Collection extends AbstractCollection
{
    /**
     * Initialize resource collection
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Designnbuy\Customer\Model\Image', 'Designnbuy\Customer\Model\ResourceModel\Image');
    }
}