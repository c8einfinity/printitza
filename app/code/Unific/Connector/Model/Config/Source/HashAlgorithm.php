<?php
/**
 * Setting the operation mode of the Unific extension
 *
 */

namespace Unific\Connector\Model\Config\Source;

class HashAlgorithm implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'sha1', 'label' => 'SHA-1'],
            ['value' => 'sha256', 'label' => 'SHA-256'],
            ['value' => 'md5', 'label' => 'MD5']
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return ['sha1' => 'SHA-1', 'sha256' => 'SHA-256', 'md5' => 'MD5'];
    }
}
