<?php

namespace Unific\Connector\Connection;

interface ConnectionInterface
{
    /**
     * Setup the connection so that its ready to send information
     * @return mixed
     */
    public function setup();

    /**
     * Call the requested method
     *
     * @return mixed
     */
    public function doRequest();

    /**
     * Handle response
     * @return mixed
     */
    public function handleResponse();
}
