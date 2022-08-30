<?php


namespace Designnbuy\Workflow\Model\ResourceModel;

class Role extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * Construct
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param string|null $resourcePrefix
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        $resourcePrefix = null
    ) {
        parent::__construct($context, $resourcePrefix);
        $this->_date = $date;
        $this->dateTime = $dateTime;
    }
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('designnbuy_workflow_role', 'id');
    }

    /**
     * Process data before saving
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $gmtDate = $this->_date->gmtDate();

        if ($object->isObjectNew() && !$object->getCreationTime()) {
            $object->setCreationTime($gmtDate);
        }
        $object->setUpdateTime($gmtDate);

        if($object->getViewStatus()){
            if(!is_array($object->getViewStatus())){
                $viewStatuses = array($object->getViewStatus());
            } else {
                $viewStatuses = $object->getViewStatus();
            }
            $object->setViewStatus(implode(',',$viewStatuses));
        }
        if($object->getUpdateStatus()){
            if(!is_array($object->getUpdateStatus())){
                $updateStatuses = array($object->getUpdateStatus());
            } else {
                $updateStatuses = $object->getUpdateStatus();
            }
            $object->setUpdateStatus(implode(',',$updateStatuses));
        }
        if($object->getAccess()){
            if(!is_array($object->getAccess())){
                $accesses = array($object->getAccess());
            } else {
                $accesses = $object->getAccess();
            }
            $object->setAccess(implode(',',$accesses));
        }
        
        return parent::_beforeSave($object);
    }
}
