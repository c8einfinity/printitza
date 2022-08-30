<?php

namespace Unific\Connector\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface HmacInterface extends ExtensibleDataInterface
{
    /**
     * @return string
     */
    public function getHmacHeader();

    /**
     * @param $hmacHeader
     * @return void
     */
    public function setHmacHeader($hmacHeader);

    /**
     * @return string
     */
    public function getHmacSecret();

    /**
     * @param $hmacSecret
     * @return void
     */
    public function setHmacSecret($hmacSecret);

    /**
     * @return string
     */
    public function getHmacAlgorithm();

    /**
     * @param $hmacAlgorithm
     * @return void
     */
    public function setHmacAlgorithm($hmacAlgorithm);
}
