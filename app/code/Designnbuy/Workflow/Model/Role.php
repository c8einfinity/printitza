<?php


namespace Designnbuy\Workflow\Model;

use Designnbuy\Workflow\Api\Data\RoleInterface;

class Role extends \Magento\Framework\Model\AbstractModel implements RoleInterface
{
    /**
     * @var string
     */
    protected $_eventObject = 'role';

    protected $_idFieldName = 'id';
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Designnbuy\Workflow\Model\ResourceModel\Role');
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
     * @param string $roleId
     * @return \Designnbuy\Workflow\Api\Data\RoleInterface
     */
    public function setId($roleId)
    {
        return $this->setData(self::ID, $roleId);
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
     * @return \Designnbuy\Workflow\Api\Data\RoleInterface
     */
    public function setTitle($Title)
    {
        return $this->setData(self::TITLE, $Title);
    }

    /**
     * get View Status
     *
     * @access public
     * @return array
     */
    public function getViewStatuses()
    {
        if (!$this->hasData('view_statuses')) {
            $this->setData('view_statuses', explode(',', $this->getData('view_status')));
        }
        return $this->getData('view_statuses');
    }

    /**
     * get Update Status
     *
     * @access public
     * @return array
     */
    public function getUpdateStatuses()
    {
        if (!$this->getData('update_statuses')) {
            $this->setData('update_statuses', explode(',', $this->getData('update_status')));
        }
        return $this->getData('update_statuses');
    }

    public function getAccesses()
    {
        if (!$this->getData('accesses')) {
            $this->setData('accesses', explode(',', $this->getData('access')));
        }
        return $this->getData('accesses');
    }
}
