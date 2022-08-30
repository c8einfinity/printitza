<?php

namespace Unific\Connector\Model\Api\Data;

class Hmac implements \Unific\Connector\Api\Data\HmacInterface
{
    /**
     * @var
     */
    protected $hmac_header;

    /**
     * @var
     */
    protected $hmac_secret;

    /**
     * @var
     */
    protected $hmac_algorithm;

    /**
     * @return mixed
     */
    public function getHmacHeader()
    {
        return $this->hmac_header;
    }

    /**
     * @param $hmac_header
     */
    public function setHmacHeader($hmac_header)
    {
        $this->hmac_header = $hmac_header;
    }

    /**
     * @return mixed
     */
    public function getHmacSecret()
    {
        return $this->hmac_secret;
    }

    /**
     * @param mixed $hmac_secret
     */
    public function setHmacSecret($hmac_secret)
    {
        $this->hmac_secret = $hmac_secret;
    }

    /**
     * @return mixed
     */
    public function getHmacAlgorithm()
    {
        return $this->hmac_algorithm;
    }

    /**
     * @param mixed $hmac_algorithm
     */
    public function setHmacAlgorithm($hmac_algorithm)
    {
        $this->hmac_algorithm = $hmac_algorithm;
    }
}
