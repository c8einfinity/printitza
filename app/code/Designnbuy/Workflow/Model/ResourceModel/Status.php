<?php


namespace Designnbuy\Workflow\Model\ResourceModel;

class Status extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
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
        $this->_init('designnbuy_workflow_status', 'status_id');
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

        if($object->getGroup()){
            if(!is_array($object->getGroup())){
                $groups = array($object->getGroup());
            } else {
                $groups = $object->getGroup();
            }
            $object->setGroup(implode(',',$groups));
        }

        return parent::_beforeSave($object);
    }
}
