<?php

namespace Lumaplaza\Calculatorshipping\Model;

/**
 * Calculatorshipping Model
 *
 * @method \Lumaplaza\Calculatorshipping\Model\Resource\Page _getResource()
 * @method \Lumaplaza\Calculatorshipping\Model\Resource\Page getResource()
 */
class Calculatorshipping extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Lumaplaza\Calculatorshipping\Model\ResourceModel\Calculatorshipping');
    }

}
