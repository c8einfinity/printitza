<?php


namespace Designnbuy\Workflow\Api\Data;

interface RoleInterface
{

    const ID = 'id';
    const TITLE = 'Title';


    /**
     * Get id
     * @return string|null
     */
    
    public function getId();

    /**
     * Set id
     * @param string $id
     * @return \Designnbuy\Workflow\Api\Data\RoleInterface
     */
    
    public function setId($roleId);

    /**
     * Get Title
     * @return string|null
     */
    
    public function getTitle();

    /**
     * Set Title
     * @param string $Title
     * @return \Designnbuy\Workflow\Api\Data\RoleInterface
     */
    
    public function setTitle($Title);
}
