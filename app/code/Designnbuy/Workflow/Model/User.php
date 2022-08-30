<?php


namespace Designnbuy\Workflow\Model;

use Designnbuy\Workflow\Api\Data\UserInterface;

class User extends \Magento\Framework\Model\AbstractModel implements UserInterface
{
    /**
     * @var string
     */
    protected $_eventObject = 'user';

    protected $_idFieldName = 'id';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Designnbuy\Workflow\Model\ResourceModel\User');
    }

    /**
     * Get id
     * @return string
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * Set id
     * @param string $userId
     * @return \Designnbuy\Workflow\Api\Data\UserInterface
     */
    public function setId($userId)
    {
        return $this->setData(self::ID, $userId);
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
     * @return \Designnbuy\Workflow\Api\Data\UserInterface
     */
    public function setTitle($Title)
    {
        return $this->setData(self::TITLE, $Title);
    }

    public function loadByUserId($userId)
    {
        $this->_getResource()->loadByUserId($this, $userId, 'user_id');
        return $this;
    }
}
