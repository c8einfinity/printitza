<?php

namespace WeSupply\Toolbox\Model;

use WeSupply\Toolbox\Api\WeSupplyApiInterface;
use Magento\Framework\App\Response\Http;

class WeSupplyApi implements WeSupplyApiInterface
{
    const GRANT_TYPE = 'client_credentials';
    const TOKEN_PATH = 'oauth/token';
    const AUTH_ORDER_PATH = 'authLinks';
    const NOTIFICATION_PATH = 'api/phone/enrol';
    const ESTIMATIONS_PATH = 'estimations';
    const SHIPPING_QUOTES_PATH = 'shippingQuotes';



    /**
     * @var string
     */
    private $protocol = 'https';

    /**
     * path to wesupply api
     * @var string
     */
    private $apiPath;

    /**
     * wesupply api client id
     * @var string
     */
    private $apiClientId;

    /**
     * wesupply api client secret
     * @var string
     */
    private $apiClientSecret;


    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     *@var \Magento\Framework\HTTP\Client\Curl
     */
    protected $curlClient;

    /**
     * @var \Magento\Catalog\Model\Session
     */
    protected $catalogSession;



    public function __construct(
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Catalog\Model\Session $catalogSession
    )
    {
        $this->curlClient = $curl;
        $this->logger = $logger;
        $this->catalogSession = $catalogSession;
    }

    /**
     * @param $protocol
     *
     */
    public function setProtocol($protocol)
    {
        $this->protocol = $protocol;
    }

    /**
     * @param $apiPath
     */
    public function setApiPath($apiPath)
    {
        $this->apiPath = $apiPath;
    }


    /**
     * @param $apiClientId
     */
    public function setApiClientId($apiClientId)
    {
        $this->apiClientId = $apiClientId;
    }


    /**
     * @param $apiClientSecret
     */
    public function setApiClientSecret($apiClientSecret)
    {
        $this->apiClientSecret = $apiClientSecret;
    }


    /**
     * @param $ipAddress
     * @param $storeId
     * @param $zipcode
     * @param $countryCode
     * @param $price
     * @param $currency
     * @param $shippers
     * @return array|bool|mixed
     */
    public function getShipperQuotes($ipAddress, $storeId, $zipcode, $countryCode, $price, $currency, $shippers)
    {

        $accesToken = $this->getToken();

        if($accesToken)
        {
            $params = array(
                "ipAddress" => $ipAddress,
                'supplierId' => $storeId,
                'postcode' => $zipcode,
                'countrycode'=>$countryCode,
                'price' => $price,
                'currency' => $currency,
                'carriers' => $shippers);

            $buildQuery = http_build_query($params);

            $this->curlClient->setOptions(
                array(
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_FOLLOWLOCATION => TRUE,
                    CURLOPT_HTTPHEADER => array("Authorization: Bearer $accesToken")
                )
            );

            try {
                $url = $this->protocol.'://'.$this->apiPath.self::SHIPPING_QUOTES_PATH.'?'.$buildQuery;
                $this->curlClient->get($url);
                $response = $this->curlClient->getBody();
                $jsonDecoded = json_decode($response, true);

                if($this->curlClient->getStatus() === Http::STATUS_CODE_403){
                    return array('error' => 'Service not available');
                }
                elseif($this->curlClient->getStatus() === Http::STATUS_CODE_503){
                    $this->logger->error('Error when checking for Shipper Quotes response: '.$response);
                    return false;
                }elseif($this->curlClient->getStatus() === Http::STATUS_CODE_200){

                    return $jsonDecoded;
                }

                $this->logger->error('Error when sending Shipper Quote to Wesupply with status: '.$this->curlClient->getStatus().' response: '.$response);
                return  false;


            }catch(\Exception $e){
                $this->logger->error("WeSupply Shipper Quotes API Error:".$e->getMessage());
                return false;
            }
        }
        return false;
    }

    /**
     * @param string $ipAddress
     * @param string $zipCode
     * expected response
     * @return array | bool;
     *
     */
    public function getEstimationsWeSupply($ipAddress, $storeId, $zipCode = '')
    {
        $accesToken = $this->getToken();

        if($accesToken)
        {
            $params = array("ipAddress" => $ipAddress, 'supplierId' => $storeId, 'postcode' => $zipCode);

            $buildQuery = http_build_query($params);

            $this->curlClient->setOptions(
                array(
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_FOLLOWLOCATION => TRUE,
                    CURLOPT_HTTPHEADER => array("Authorization: Bearer $accesToken")
                )
            );

            try {
                $url = $this->protocol.'://'.$this->apiPath.self::ESTIMATIONS_PATH.'?'.$buildQuery;
                $this->curlClient->get($url);
                $response = $this->curlClient->getBody();
                $jsonDecoded = json_decode($response, true);

                if($this->curlClient->getStatus() === Http::STATUS_CODE_403){
                    $this->logger->error('Estimations WeSupply - service not available');
                    return false;
                }
                elseif($this->curlClient->getStatus() === Http::STATUS_CODE_503){
                    $this->logger->error('Error when contacting Estimations Wesupply response: '.$response);
                    return false;
                }elseif($this->curlClient->getStatus() === Http::STATUS_CODE_200){

                    return $jsonDecoded ?? false;
                }

                $this->logger->error('Error when contacting Estimations Wesupply with status: '.$this->curlClient->getStatus().' response: '.$response);
                return  false;

            }catch(\Exception $e){
                $this->logger->error("WeSupply Estimations API Error:".$e->getMessage());
                return false;
            }
        }

        return false;

    }

    /**
     * @param $orderNo
     * @param $clientPhone
     * @return bool|mixed
     */
    public function notifyWeSupply($orderNo, $clientPhone)
    {
        $params = array("order" => $orderNo, "phone" => $clientPhone);
        $buildQuery = http_build_query($params);

        $this->curlClient->setOptions(
            array(
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_FOLLOWLOCATION => TRUE
            )
        );
        try {
            $url = $this->protocol.'://'.$this->apiPath.self::NOTIFICATION_PATH.'?'.$buildQuery;
            $this->curlClient->get($url);
            $response = $this->curlClient->getBody();

            $jsonDecoded = json_decode($response, true);

            if($this->curlClient->getStatus() === Http::STATUS_CODE_403){
                return array('error' => 'Service not available');
            }
            elseif($this->curlClient->getStatus() === Http::STATUS_CODE_503){
                $this->logger->error('Error when sending SMS notif to Wesupply response: '.$response);
                return $jsonDecoded;
            }elseif($this->curlClient->getStatus() === Http::STATUS_CODE_200){

                return true;
            }

            $this->logger->error('Error when sending SMS notif to Wesupply with status: '.$this->curlClient->getStatus().' response: '.$response);
            return  false;

        }catch(\Exception $e){
            $this->logger->error("WeSupply Notification Error:".$e->getMessage());
            return false;
        }
    }


    /**
     * @return bool|string
     */
    public function weSupplyAccountCredentialsCheck()
    {
        $this->catalogSession->unsGeneratedToken();

        $accesToken = $this->getToken();
        return !empty($accesToken) ? true : false;
    }

    /**
     * @param $externalOrderIdString
     * @return bool|mixed
     */
    public function weSupplyInterogation($externalOrderIdString)
    {
        $accesToken = $this->getToken();

        if($accesToken)
        {
            $params = array("orders"=>$externalOrderIdString);

            $buildQuery = http_build_query($params);

            $this->curlClient->setOptions(
                array(
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_FOLLOWLOCATION => TRUE,
                CURLOPT_HTTPHEADER => array("Authorization: Bearer $accesToken")
                )
            );

            try {
                $url = $this->protocol.'://'.$this->apiPath.self::AUTH_ORDER_PATH.'?'.$buildQuery;
                $this->curlClient->get($url);
                $response = $this->curlClient->getBody();
                $jsonDecoded = json_decode($response, true);

                if($this->curlClient->getStatus() === Http::STATUS_CODE_403){
                    $this->logger->error('Wesupply Order Interogation - service not available');
                    return false;
                }
                elseif($this->curlClient->getStatus() === Http::STATUS_CODE_503){
                    $this->logger->error('Error when interogating orders at Wesupply response: '.$response);
                    return false;
                }elseif($this->curlClient->getStatus() === Http::STATUS_CODE_200){

                    return $jsonDecoded ?? false;
                }

                $this->logger->error('Wesupply Order Interogation error with status: '.$this->curlClient->getStatus().' response: '.$response);
                return  false;

            }catch(\Exception $e){
                $this->logger->error("WeSupply Order Interogation API Error:".$e->getMessage());
                return false;
            }
         }

        return false;
    }


    /**
     * we check if we already have a previous token in session and we return it, otherwise we generate a new one
     * @return mixed|string
     */
    private function getToken()
    {
        $generatedToken = $this->catalogSession->getGeneratedToken();

        if(is_array($generatedToken))
        {
            $generatedTime = isset($generatedToken['created_at']) ? $generatedToken['created_at'] : '';
            $token = isset($generatedToken['token']) ? $generatedToken['token'] : '';

            if(empty($generatedTime) || empty($token)){
                $this->catalogSession->unsGeneratedToken();
                $token = $this->generateNewToken();
                if(!empty($token)){
                    $this->setTokenInSession($token);
                }
                return $token;
            }

            if( (time() - $generatedTime) > 3500){
                $this->catalogSession->unsGeneratedToken();
                $token = $this->generateNewToken();
                if(!empty($token)){
                    $this->setTokenInSession($token);
                }
                return $token;
            }

            return $token;
        }

        $token = $this->generateNewToken();
        if(!empty($token)){
            $this->setTokenInSession($token);
        }
        return $token;

    }


    /**
     * sets the token in session for further usage
     * @param $token
     */
    private function setTokenInSession($token)
    {
        $sessionToken = ['created_at'=> time(), 'token'=> $token];
        $this->catalogSession->setGeneratedToken($sessionToken);
    }

    /**
     * generates a new token from WeSupply
     * @return string
     */
    private function generateNewToken()
    {

        $authUrl = $this->protocol.'://'.$this->apiPath.self::TOKEN_PATH;

        $userData = array(
            "grant_type"    => self::GRANT_TYPE,
            "client_id"     => $this->apiClientId,
            "client_secret" => $this->apiClientSecret
        );

        $this->curlClient->setHeaders(array('Content-Type: application/x-www-form-urlencoded'));

        try{
            $this->curlClient->post($authUrl, $userData);
            $response = $this->curlClient->getBody();
            $jsonDecoded = json_decode($response);

            if($this->curlClient->getStatus() === Http::STATUS_CODE_403){
                $this->logger->error("WeSupply Token Request Not Available");
                return '';
            }
            elseif($this->curlClient->getStatus() === Http::STATUS_CODE_503){
                $this->logger->error('Error while generating token from WeSupply response: '.$response);
                return '';
            }elseif($this->curlClient->getStatus() === Http::STATUS_CODE_200){

                return $jsonDecoded->access_token ?? '';
            }

            $this->logger->error('Error when sending Token Request to Wesupply with status: '.$this->curlClient->getStatus().' response: '.$response);

            return  '';

        }catch(\Exception $e){
            $this->logger->error("WeSupply API Error:".$e->getMessage());
            return '';
        }

    }



}