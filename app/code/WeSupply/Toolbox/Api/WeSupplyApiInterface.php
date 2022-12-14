<?php
namespace WeSupply\Toolbox\Api;


interface WeSupplyApiInterface
{

    /**
     * @param $externalOrderIdString
     * @return mixed
     */
    function weSupplyInterogation($externalOrderIdString);

    /**
     * @param $orderNo
     * @param $clientPhone
     * @return mixed
     */
    function notifyWeSupply($orderNo, $clientPhone);

    /**
     * @param $ipAddress
     * @param $storeId
     * @param string $zipCode
     * @return mixed
     */
    function getEstimationsWeSupply($ipAddress, $storeId, $zipCode = '');

    /**
     * @param $protocol
     */
    public function setProtocol($protocol);

    /**
     * @param $apiPath
     * @return mixed
     */
    function setApiPath($apiPath);

    /**
     * @param $apiClientId
     * @return mixed
     */
    function setApiClientId($apiClientId);

    /**
     * @param $apiClientSecret
     * @return mixed
     */
    function setApiClientSecret($apiClientSecret);

    /**
     * @param $ipAddress
     * @param $storeId
     * @param $zipcode
     * @param $countryCode
     * @param $price
     * @param $currency
     * @param $shippers
     * @return mixed
     */
    function getShipperQuotes($ipAddress, $storeId, $zipcode, $countryCode, $price, $currency, $shippers);


    /**
     * @return mixed
     */
    function weSupplyAccountCredentialsCheck();
}
