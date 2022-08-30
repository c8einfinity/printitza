<?php

namespace Unific\Connector\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

interface IntegrationInterface extends ExtensibleDataInterface
{
    /**
     * @return string
     */
    public function getIntegrationId();

    /**
     * @param $id
     * @return void
     */
    public function setIntegrationId($id);

    /**
     * @return string
     */
    public function getEndpoint();

    /**
     * @param $endpoint
     * @return void
     */
    public function setEndpoint($endpoint);
}
