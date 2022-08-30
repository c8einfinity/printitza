<?php
/**
 * Setting the message queue processor batch size
 *
 */

namespace Unific\Connector\Model\Config\Source;

class PageSize implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        for ($i = 1; $i <= 25; $i++) {
            $options[] = ['value' => $i, 'label' => $i];
        }

        return $options;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $options = [];
        foreach ($this->toOptionArray() as $option) {
            $options[$option['value']] = $options['label'];
        }

        return $options;
    }
}
