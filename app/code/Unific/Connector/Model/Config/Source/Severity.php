<?php
/**
 * Setting the operation mode of the Unific extension
 *
 */

namespace Unific\Connector\Model\Config\Source;

class Severity implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => 'all', 'label' => 'All Webhooks'], ['value' => 'failed', 'label' => 'Failed Webhooks']];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return ['all' => 'All Webhooks', 'failed' => 'Failed Webhooks'];
    }
}
