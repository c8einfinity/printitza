<?php

namespace Unific\Connector\Connection;

class Connection implements ConnectionInterface
{
    protected $urlData = [];

    protected $httpClient;
    protected $httpRequestFactory;
    protected $httpHeadersFactory;

    /**
     * Connection constructor.
     *
     * @param \Zend\Http\Client $httpClient
     * @param \Zend\Http\RequestFactory $httpRequestFactory
     * @param \Zend\Http\HeadersFactory $httpHeadersFactory
     */
    public function __construct(
        \Zend\Http\Client $httpClient,
        \Zend\Http\RequestFactory $httpRequestFactory,
        \Zend\Http\HeadersFactory $httpHeadersFactory
    ) {
        $this->httpClient = $httpClient;
        $this->httpRequestFactory = $httpRequestFactory;
        $this->httpHeadersFactory = $httpHeadersFactory;
    }

    public function setup()
    {
        return $this;
    }

    public function doRequest()
    {
        return $this;
    }

    public function handleResponse()
    {
        return $this;
    }

    /**
     * @return mixed|string
     */
    public function getRestPath()
    {
        return (isset($this->urlData['query']))
            ? $this->urlData['path'] . '?' . urlencode($this->urlData['query']) : $this->urlData['path'];
    }

    /**
     * @param $url
     * @param array $data
     * @param array $extraHeaders
     * @param $requestType
     * @return \Zend\Http\Response
     */
    public function sendData($url, $data = [], $extraHeaders = [], $requestType = \Zend\Http\Request::METHOD_POST)
    {
        $this->httpClient->setOptions(['sslverifypeer' => null, 'sslallowselfsigned' => true]);
        $this->httpClient->setAdapter(new \Zend\Http\Client\Adapter\Curl());
        $this->httpClient->setEncType(\Zend\Http\Client::ENC_FORMDATA);
        $this->httpClient->setUri($url);

        $httpHeaders = $this->httpHeadersFactory->create();
        $httpHeaders->addHeaders($extraHeaders);

        $httpRequest = $this->httpRequestFactory->create();
        $httpRequest->setUri($url);
        $httpRequest->setMethod($requestType);
        $httpRequest->setHeaders($httpHeaders);

        switch ($requestType) {
            case \Zend\Http\Request::METHOD_POST:
                $httpRequest->setContent(json_encode($data));
                break;
            case \Zend\Http\Request::METHOD_PUT:
                $httpRequest->setContent(json_encode($data));
                break;
            default:
                $httpRequest->setQuery($data);
                break;
        }

        return $this->httpClient->send($httpRequest);
    }

    /**
     * @param $url
     * @param array $data
     * @param array $headers
     * @return mixed
     */
    public function post($url, $data = [], $headers = [])
    {
        return $this->sendData($url, $data, $headers, \Zend\Http\Request::METHOD_POST);
    }

    /**
     * @param $url
     * @param array $data
     * @param array $headers
     * @return mixed
     */
    public function get($url, $data = [], $headers = [])
    {
        return $this->sendData($url, $data, $headers, \Zend\Http\Request::METHOD_GET);
    }

    /**
     * @param $url
     * @param array $data
     * @param array $headers
     * @return mixed
     */
    public function put($url, $data = [], $headers = [])
    {
        return $this->sendData($url, $data, $headers, \Zend\Http\Request::METHOD_PUT);
    }

    /**
     * @param $url
     * @param array $data
     * @param array $headers
     * @return mixed
     */
    public function delete($url, $data = [], $headers = [])
    {
        return $this->sendData($url, $data, $headers, \Zend\Http\Request::METHOD_DELETE);
    }
}
