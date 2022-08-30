<?php

/**
 * Calculatorshipping Resource Collection
 */
namespace Lumaplaza\Calculatorshipping\Model\ResourceModel\Calculatorshipping;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Lumaplaza\Calculatorshipping\Model\Calculatorshipping', 'Lumaplaza\Calculatorshipping\Model\ResourceModel\Calculatorshipping');
    }
}
