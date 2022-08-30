<?php


namespace Designnbuy\Workflow\Model;

use Designnbuy\Workflow\Api\Data\StatusInterface;

class Status extends \Magento\Framework\Model\AbstractModel implements StatusInterface
{
    /**
     * @var string
     */
    protected $_eventObject = 'status';

    protected $_idFieldName = 'status_id';
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Designnbuy\Workflow\Model\ResourceModel\Status');
    }

    /**
     * Get status_id
     * @return string
     */
    public function getStatusId()
    {
        return $this->getData(self::STATUS_ID);
    }

    /**
     * Set status_id
     * @param string $statusId
     * @return \Designnbuy\Workflow\Api\Data\StatusInterface
     */
    public function setStatusId($statusId)
    {
        return $this->setData(self::STATUS_ID, $statusId);
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
     * @return \Designnbuy\Workflow\Api\Data\StatusInterface
     */
    public function setTitle($Title)
    {
        return $this->setData(self::TITLE, $Title);
    }
}
