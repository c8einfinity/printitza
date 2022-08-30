<?php

namespace Unific\Connector\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

class Filter extends \Magento\Framework\App\Helper\AbstractHelper
{
    const ADDITIONAL_ORDER_PAYMENT_FIELDS = 'unific/additional_data/order_data/order_payment_attributes';

    protected $paymentWhitelist = [
        'method',
        'additional_data',
        'additional_information',
        'po_number',
        'amount_ordered',
        'shipping_amount'
    ];

    /**
     * @param int $storeId
     * @return array
     */
    public function getPaymentWhitelist($storeId = null)
    {
        $additionalFields = $this->getAdditionalFields(self::ADDITIONAL_ORDER_PAYMENT_FIELDS, $storeId);

        return array_merge($this->paymentWhitelist, $additionalFields);
    }

    /**
     * @param array $data
     * @param array $processObjectsMap
     * @return array
     */
    public function processObjectData(array $data, &$processObjectsMap = [])
    {
        $result = [];
        foreach ($data as $key => $value) {
            if (!is_object($value) && !is_array($value)) {
                $result[$key] = $value;

                continue;
            }

            $childData = null;
            if (is_array($value)) {
                $childData = $this->processObjectData($value, $processObjectsMap);
            }
            if (is_object($value)) {
                $objectId = spl_object_hash($value);
                // prevent infinite recursive loop
                if (array_key_exists($objectId, $processObjectsMap)) {
                    continue;
                }

                $processObjectsMap[$objectId] = true;
                if (method_exists($value, 'getData')) {
                    $childData = $this->processObjectData($value->getData(), $processObjectsMap);
                } elseif (method_exists($value, '__toString')) {
                    $childData = $value->__toString();
                }
            }

            if (!empty($childData)) {
                $result[$key] = $childData;
            }

        }

        return $result;
    }

    /**
     * Ensure the data is always sanitized
     *
     * @param array $returnData
     * @return array
     */
    public function sanitizeAddressData($returnData)
    {
        if (isset($returnData['addresses'])) {
            foreach ($returnData['addresses'] as &$address) {
                if (isset($address['0'])) {
                    $address['street'] = $address['0'];
                    unset($address['0']);
                }
            }
        }

        return $returnData;
    }

    public function fixAddressKey($returnData, $identifier = 'street')
    {
        for ($i=0; $i<10; $i++) {
            if (isset($returnData[$i])) {
                $arrayKey = ($i>0) ? $identifier . $i : $identifier;
                $returnData[$arrayKey] = $returnData[$i];
                unset($returnData[$i]);
            }
        }

        return $returnData;
    }

    /**
     * @param string $key
     * @param int|null $storeId
     * @return array
     */
    protected function getAdditionalFields(string $key, int $storeId = null)
    {
        $configData = $this->scopeConfig->getValue(
            $key,
            ScopeInterface::SCOPE_STORES,
            $storeId
        );
        if (!$configData) {
            return [];
        }

        return array_map(function ($field) {
            return trim($field);
        }, explode("\n", $configData));
    }
}
