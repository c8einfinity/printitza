<?php

namespace Unific\Connector\Helper\Data;

use Magento\Framework\Api\CustomAttributesDataInterface;
use Magento\Framework\Api\ExtensibleDataInterface;
use Magento\Framework\Webapi\ServiceOutputProcessor;

class Converter extends ServiceOutputProcessor
{
    /**
     * Convert data object to array and process available custom attributes
     *
     * @param array $dataObjectArray
     * @return array
     */
    protected function processDataObject($dataObjectArray)
    {
        $dataObjectArray = parent::processDataObject($dataObjectArray);

        if (array_key_exists(ExtensibleDataInterface::EXTENSION_ATTRIBUTES_KEY, $dataObjectArray)) {
            if (!array_key_exists(CustomAttributesDataInterface::CUSTOM_ATTRIBUTES, $dataObjectArray)) {
                $dataObjectArray[CustomAttributesDataInterface::CUSTOM_ATTRIBUTES] = [];
            }
            foreach ($dataObjectArray[CustomAttributesDataInterface::CUSTOM_ATTRIBUTES] as &$customAttribute) {
                if (is_object($customAttribute['value']) || is_array($customAttribute['value'])) {
                    $customAttribute['value'] = json_encode($customAttribute['value']);
                }
            }
            foreach ($dataObjectArray[ExtensibleDataInterface::EXTENSION_ATTRIBUTES_KEY] as $code => $value) {
                $dataObjectArray[CustomAttributesDataInterface::CUSTOM_ATTRIBUTES][] = [
                    'attribute_code' => $code,
                    'value' => is_object($value) || is_array($value) ? json_encode($value) : $value
                ];
            }
            unset($dataObjectArray[ExtensibleDataInterface::EXTENSION_ATTRIBUTES_KEY]);
        }

        return $dataObjectArray;
    }
}