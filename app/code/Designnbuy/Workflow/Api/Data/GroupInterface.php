<?php


namespace Designnbuy\Workflow\Api\Data;

interface GroupInterface
{

    const TITLE = 'Title';
    const GROUP_ID = 'group_id';


    /**
     * Get group_id
     * @return string|null
     */
    
    public function getGroupId();

    /**
     * Set group_id
     * @param string $group_id
     * @return \Designnbuy\Workflow\Api\Data\GroupInterface
     */
    
    public function setGroupId($groupId);

    /**
     * Get Title
     * @return string|null
     */
    
    public function getTitle();

    /**
     * Set Title
     * @param string $Title
     * @return \Designnbuy\Workflow\Api\Data\GroupInterface
     */
    
    public function setTitle($Title);
}
