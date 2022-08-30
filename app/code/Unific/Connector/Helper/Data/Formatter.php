<?php

namespace Unific\Connector\Helper\Data;


class Formatter extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @param array $returnData
     * @param $originalStreet
     * @param string $identifier
     * @return array
     */
    public function setStreetData(array $returnData, $originalStreet, $identifier = 'street')
    {
        // Since the street can be parsed by different functions,
        // sometimes its an multi array and sometimes its already an imploded string
        if (is_array($originalStreet)) {
            $originalStreet = implode("\n", $originalStreet);
        }

        if (strpos($originalStreet, "\n") !== false) {
            $streetInfo = explode("\n", $originalStreet);
            $lineCount = count($streetInfo);

            for ($i=0; $i < $lineCount; $i++) {
                $arrayKey = ($i>0) ? "$identifier$i" : $identifier;

                $returnData[$arrayKey] = $streetInfo[$i];
            }
        } else {
            $returnData[$identifier] = $originalStreet;
        }

        return $returnData;
    }
}
