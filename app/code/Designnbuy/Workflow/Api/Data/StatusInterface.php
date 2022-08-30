<?php


namespace Designnbuy\Workflow\Api\Data;

interface StatusInterface
{

    const TITLE = 'title';
    const STATUS_ID = 'status_id';


    /**
     * Get status_id
     * @return string|null
     */
    
    public function getStatusId();

    /**
     * Set status_id
     * @param string $status_id
     * @return \Designnbuy\Workflow\Api\Data\StatusInterface
     */
    
    public function setStatusId($statusId);

    /**
     * Get Title
     * @return string|null
     */
    
    public function getTitle();

    /**
     * Set Title
     * @param string $Title
     * @return \Designnbuy\Workflow\Api\Data\StatusInterface
     */
    
    public function setTitle($Title);
}
