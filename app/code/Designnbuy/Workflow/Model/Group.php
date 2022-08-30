<?php


namespace Designnbuy\Workflow\Model;

use Designnbuy\Workflow\Api\Data\GroupInterface;

class Group extends \Magento\Framework\Model\AbstractModel implements GroupInterface
{
    /**
     * @var string
     */
    protected $_eventObject = 'group';

    protected $_idFieldName = 'group_id';
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Designnbuy\Workflow\Model\ResourceModel\Group');
    }

    /**
     * Get group_id
     * @return string
     */
    public function getGroupId()
    {
        return $this->getData(self::GROUP_ID);
    }

    /**
     * Set group_id
     * @param string $groupId
     * @return \Designnbuy\Workflow\Api\Data\GroupInterface
     */
    public function setGroupId($groupId)
    {
        return $this->setData(self::GROUP_ID, $groupId);
    }

    /**
     * Get Title
     * @return string
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    /**
     * Set Title
     * @param string $Title
     * @return \Designnbuy\Workflow\Api\Data\GroupInterface
     */
    public function setTitle($Title)
    {
        return $this->setData(self::TITLE, $Title);
    }
}
