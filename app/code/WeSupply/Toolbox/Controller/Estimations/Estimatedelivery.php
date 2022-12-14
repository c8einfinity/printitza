<?php
/**
 * Created by PhpStorm.
 * User: adminuser
 * Date: 28.09.2018
 * Time: 13:49
 */

namespace WeSupply\Toolbox\Controller\Estimations;

use Magento\Framework\App\Response\Http;
use Magento\Framework\Controller\ResultFactory;

class Estimatedelivery extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \WeSupply\Toolbox\Helper\Data
     */
    protected $helper;

    /**
     * @var \WeSupply\Toolbox\Api\WeSupplyApiInterface
     */
    protected $weSupplyApi;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Catalog\Model\Session
     */
    protected $catalogSession;

    /**
     * @var string
     */
    protected $price;

    /**
     * @var string
     */
    protected $currency;

    /**
     * @var string
     */
    private $ipAddress;

    /**
     * @var string
     */
    private $storeId;

    /**
     * @var string
     */
    private $postCode;

    /**
     * @var string
     */
    private $countrycode;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;



    /**
     * Estimatedelivery constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \WeSupply\Toolbox\Helper\Data $helper
     * @param \WeSupply\Toolbox\Api\WeSupplyApiInterface $weSupplyApi
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param \Magento\Catalog\Model\Session $catalogSession
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \WeSupply\Toolbox\Helper\Data $helper,
        \WeSupply\Toolbox\Api\WeSupplyApiInterface $weSupplyApi,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Magento\Catalog\Model\Session $catalogSession,
        \Psr\Log\LoggerInterface $logger

    )
    {
        parent::__construct($context);
        $this->helper = $helper;
        $this->weSupplyApi = $weSupplyApi;
        $this->resultJsonFactory = $jsonFactory;
        $this->catalogSession = $catalogSession;
        $this->logger = $logger;

    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        try {

            $params = $this->getRequest()->getParams();
            $validation = $this->_validateParams($params);

            if ($validation) {
                return $result->setData(['success' => false, 'error' => $validation]);
            }

            $sessionEstimationsData = $this->catalogSession->getEstimationsData();

            if ($sessionEstimationsData) {
                $estimationsArr = unserialize($sessionEstimationsData);

                if ($this->postCode) {
                    if (array_key_exists($this->postCode, $estimationsArr)) {
                        if (isset($estimationsArr[$this->postCode]['estimated_arrival'])) {
                            $estimationsArr['default'] = $this->postCode;
                            $this->catalogSession->setEstimationsData(serialize($estimationsArr));
                            $estimatedDelivery = $estimationsArr[$this->postCode]['estimated_arrival'];
                            $countrycode = $estimationsArr[$this->postCode]['countrycode'];
                            $country = $this->helper->getCountryname($countrycode);

                            return $result->setData(['success' => true, 'estimatedDelivery' => $estimatedDelivery, 'zipcode' => $this->postCode, 'country' => $country]);
                        }
                    }
                }
            }

            $newEstimations = $this->getShipperQuotes();

            if (is_array($newEstimations) && count($newEstimations) > 0) {
                $processResult = $this->processShipperQuotes($newEstimations);
                return $result->setData($processResult);
            }

            return $result->setData(['success' => false, 'error' => 'Error ocurred while communicating with WeSupply API']);

        }catch(\Exception $ex){
            $this->logger->error("Error on WeSupply Estimations: " . $ex->getMessage());
            return $result->setData(['success' => false, 'error' => 'Error ocurred while communicating with WeSupply API']);
        }

    }





    /**
     * processing the newEstimations and including them in the session
     * @param $newEstimations
     * @return array
     */
    protected function processShipperQuotes($newEstimations)
    {
        if (isset($newEstimations['shipper']) && is_array($newEstimations['shipper']) && count($newEstimations['shipper']) > 0) {
            $newZipCode = $newEstimations['zip'];
            $estimationsFormat = $this->helper->getDeliveryEstimationsFormat() ? $this->helper->getDeliveryEstimationsFormat() : 'd F';
            $estimationsRange = $this->helper->getDeliveryEstimationsRange() ? $this->helper->getDeliveryEstimationsRange() : 0;

            $defaultCarriers = $this->helper->getEstimationsDefaultCarrierAndMethod();

            /** we are defaulting to first shipper and first method if the default one is not found */
            reset($newEstimations['shipper']);
            $fistCarrierCode = key($newEstimations['shipper']);
            $firstCarrierMethod = key($newEstimations['shipper'][$fistCarrierCode]);
            $estimationTimestamp = $newEstimations['shipper'][$fistCarrierCode][$firstCarrierMethod];

            if(is_array($defaultCarriers)){
                $defaultCCode = $defaultCarriers['carrier'];
                $defaultCM = $defaultCarriers['method'];

                if(isset($newEstimations['shipper'][$defaultCCode])){
                    reset($newEstimations['shipper'][$defaultCCode]);
                    $firstCarrierCode = key($newEstimations['shipper'][$defaultCCode]);
                    $estimationTimestamp = $newEstimations['shipper'][$defaultCCode][$firstCarrierCode];

                    foreach($newEstimations['shipper'][$defaultCCode] as $quotedMethods => $quoteTmstValues){
                        if($defaultCM == $quotedMethods){
                            $estimationTimestamp = $quoteTmstValues;
                            break;
                        }
                    }
                }
            }

            $estimatedDelivery = date($estimationsFormat, $estimationTimestamp);

            if ($estimationsRange > 0) {
                $estimatedRange = date($estimationsFormat, strtotime('+' . $estimationsRange . ' days', $estimationTimestamp));
                $estimatedDelivery .= ' - ' . $estimatedRange;
            }

            $countrycode = $newEstimations['countrycode'];
            $country = $this->helper->getCountryname($countrycode);
            $newEstimations['estimated_arrival'] = $estimatedDelivery;
            $newEstimations['shipper'] = $this->helper->revertWesupplyQuotesToMag($newEstimations['shipper']);

            $this->helper->setEstimationsData($newEstimations);

            return ['success' => true, 'estimatedDelivery' => $estimatedDelivery, 'zipcode' => $newZipCode, 'country' => $country];
        }

        return ['success' => false, 'error' => 'Error ocurred while communicating with WeSupply API'];
    }

    /**
     * @return mixed
     * get estimates from wesupply
     */
    protected function getEstimates()
    {
        $apiPath = $this->helper->getWeSupplySubDomain() . '.' . $this->helper->getWeSupplyDomain() . '/api/';
        $this->weSupplyApi->setProtocol($this->helper->getProtocol());
        $this->weSupplyApi->setApiPath($apiPath);
        $this->weSupplyApi->setApiClientId($this->helper->getWeSupplyApiClientId());
        $this->weSupplyApi->setApiClientSecret($this->helper->getWeSupplyApiClientSecret());

        return $this->weSupplyApi->getEstimationsWeSupply($this->ipAddress, $this->storeId, $this->postCode);
    }


    /**
     * @return array|mixed
     */
    protected function getShipperQuotes()
    {
        $apiPath = $this->helper->getWeSupplySubDomain() . '.' . $this->helper->getWeSupplyDomain() . '/api/';
        $this->weSupplyApi->setProtocol($this->helper->getProtocol());
        $this->weSupplyApi->setApiPath($apiPath);
        $this->weSupplyApi->setApiClientId($this->helper->getWeSupplyApiClientId());
        $this->weSupplyApi->setApiClientSecret($this->helper->getWeSupplyApiClientSecret());

        $carrierCodes = $this->helper->getMappedShippingMethods();

        if(count($carrierCodes) === 0){
            return [];
        }

        return $this->weSupplyApi->getShipperQuotes($this->ipAddress, $this->storeId, $this->postCode, $this->countrycode, $this->price, $this->currency, $carrierCodes);
    }


    /**
     * @param $params
     * @return bool|\Magento\Framework\Phrase
     */
    protected function _validateParams($params)
    {

        $ipAddress = isset($params['customerIp']) ? $params['customerIp'] : false;
        $storeId = isset($params['storeId']) ? $params['storeId'] : false;
        $price = isset($params['price']) ? $params['price'] : false;
        $currency = isset($params['currency']) ? $params['currency'] : false;

        $postCode = isset($params['postcode']) ? $params['postcode'] : '';
        $countrycode = isset($params['countrycode']) ? $params['countrycode'] : '';


        if ($ipAddress === false) {
            return __('Ip address missing');
        }
        if ($storeId === false) {
            return __('Store is missing');
        }
        if ($price === false) {
            return __('Price is missing');
        }
        if ($currency === false) {
            return __('Currency is missing');
        }

        $this->ipAddress = $ipAddress;
        $this->storeId = $storeId;
        $this->postCode = $postCode;
        $this->countrycode = $countrycode;
        $this->price = $price;
        $this->currency = $currency;

        return false;
    }

}