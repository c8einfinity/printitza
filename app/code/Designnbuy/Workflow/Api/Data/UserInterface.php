<?php


namespace Designnbuy\Workflow\Api\Data;

interface UserInterface
{

    const TITLE = 'Title';
    const ID = 'id';


    /**
     * Get id
     * @return string|null
     */
    
    public function getId();

    /**
     * Set id
     * @param string $id
     * @return \Designnbuy\Workflow\Api\Data\UserInterface
     */
    
    public function setId($userId);

    /**
     * Get Title
     * @return string|null
     */
    
    public function getTitle();

    /**
     * Set Title
     * @param string $Title
     * @return \Designnbuy\Workflow\Api\Data\UserInterface
     */
    
    public function setTitle($Title);
}
