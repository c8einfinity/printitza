<?php

namespace Unific\Connector\Api;

interface SetupManagementInterface
{
    /**
     * Returns the connection data
     *
     * @api
     *
     * @param Data\IntegrationInterface $integration
     * @return \Unific\Connector\Api\Data\SetupResponseInterface
     */
    public function getData(\Unific\Connector\Api\Data\IntegrationInterface $integration);
}
