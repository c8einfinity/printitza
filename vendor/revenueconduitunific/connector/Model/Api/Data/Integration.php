<?php

namespace Unific\Connector\Model\Api\Data;

class Integration implements \Unific\Connector\Api\Data\IntegrationInterface
{
    /**
     * @var
     */
    protected $integration_id;

    protected $endpoint;

    /**
     * @return string
     */
    public function getIntegrationId()
    {
        return $this->integration_id;
    }

    /**
     * @param $id
     */
    public function setIntegrationId($id)
    {
        $this->integration_id = $id;
    }

    /**
     * @return mixed
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * @param mixed $endpoint
     */
    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;
    }
}
