<?php

namespace Unific\Connector\Api\Data;

interface QueueInterface
{
    /**
     * @return string
     */
    public function getGuid();

    /**
     * @param string $guid
     * @return void
     */
    public function setGuid($guid);

    /**
     * @return string
     */
    public function getMessage();

    /**
     * @param $message
     * @return void
     */
    public function setMessage($message);

    /**
     * @return string
     */
    public function getHeaders();

    /**
     * @param $headers
     * @return mixed
     */
    public function setHeaders($headers);

    /**
     * @return string
     */
    public function getUrl();

    /**
     * @param $url
     * @return void
     */
    public function setUrl($url);

    /**
     * @return int
     */
    public function getHistorical();

    /**
     * @param bool $historical
     * @return void
     */
    public function setHistorical($historical = false);

    /**
     * @return string
     */
    public function getRequestType();

    /**
     * @param string $requestType
     * @return void
     */
    public function setRequestType($requestType = 'get');

    /**
     * @return int
     */
    public function getRetryAmount();

    /**
     * @param int $retryAmount
     * @return void
     */
    public function setRetryAmount($retryAmount = 0);

    /**
     * @return int
     */
    public function getMaxRetryAmount();

    /**
     * @param int $maxRetryAmount
     * @return void
     */
    public function setMaxRetryAmount($maxRetryAmount = 20);

    /**
     * @return string
     */
    public function getResponseError();

    /**
     * @param string $responseError
     * @return void
     */
    public function setResponseError($responseError = '');

    /**
     * @return int
     */
    public function getResponseHttpCode();

    /**
     * @param int $responseHttpCode
     * @return void
     */
    public function setResponseHttpCode($responseHttpCode = 200);

    /**
     * @return int
     */
    public function getRequestDateFirst();

    /**
     * @param $dateFirst
     * @return void
     */
    public function setRequestDateFirst($dateFirst);

    /**
     * @return int
     */
    public function getRequestDateLast();

    /**
     * @param $dateLast
     * @return void
     */
    public function setRequestDateLast($dateLast);

    /**
     * @param int $status
     * @return void
     */
    public function setStatus(int $status);

    /**
     * @return int
     */
    public function getStatus();

    /**
     * @param int $timestamp
     * @return void
     */
    public function setStatusChange(int $timestamp = null);

    /**
     * @return int
     */
    public function getStatusChange();
}
