<?php

namespace Unific\Connector\Api;

interface QueueRepositoryInterface
{
    /**
     * Create or update a data
     * @param Data\QueueInterface $queue
     * @return void
     */
    public function save(Data\QueueInterface $queue);

    /**
     * @param $queueGuid
     * @return Data\QueueInterface $queue
     */
    public function getById($queueGuid);

    /**
     * Delete test.
     * @param Data\QueueInterface $queue
     * @return bool
     */
    public function delete(Data\QueueInterface $queue);

    /**
     * Delete test by ID.
     * @return bool
     */
    public function deleteById($queueGuid);

    /**
     * Truncate the historical queue
     * @return bool
     */
    public function truncateHistorical();

    /**
     * Truncate the queue
     * @return bool
     */
    public function truncateQueue();
}
