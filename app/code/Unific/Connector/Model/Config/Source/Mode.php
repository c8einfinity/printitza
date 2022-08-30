<?php
/**
 * Setting the operation mode of the Unific extension
 *
 */

namespace Unific\Connector\Model\Config\Source;

class Mode implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => 'live', 'label' => 'Live'], ['value' => 'burst', 'label' => 'Burst']];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return ['live' => 'Live', 'burst' => 'Burst'];
    }
}
