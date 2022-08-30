<?php

namespace Unific\Connector\Api;

interface HistoricalManagementInterface
{
    /**
     * Triggers the historical process
     *
     * @api
     *
     * @return bool true on success
     */
    public function triggerHistorical();

    /**
     * Triggers the historical process
     *
     * @api
     *
     * @param string $type
     * @return bool true on success
     */
    public function triggerHistoricalForType($type);

    /**
     * Stop the historical process
     *
     * @api
     *
     * @return bool true on success
     */
    public function stopHistorical();

    /**
     * Stop the historical process for a given type
     *
     * @api
     *
     * @param string $type
     * @return bool true on success
     */
    public function stopHistoricalForType($type);
}
