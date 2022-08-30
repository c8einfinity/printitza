<?php

namespace Designnbuy\Commission\Model\ResourceModel\Redemption;

/**
 * Designer Redemption collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    const ADMIN_USER = 1;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * @var int
     */
    protected $_storeId;


    /**
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     */
    public function __construct(
        \Designnbuy\Commission\Helper\Data $commissionHelper,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->commissionHelper = $commissionHelper;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->_date = $date;
        $this->_storeManager = $storeManager;
    }

    /**
     * Constructor
     * Configures collection
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Designnbuy\Commission\Model\Redemption', 'Designnbuy\Commission\Model\ResourceModel\Redemption');
        $this->_map['fields']['entity_id'] = 'main_table.entity_id';
    }

    protected function _initSelect()
    {
        $ownerUserId = $this->commissionHelper->getOwnerUserId();
        $areaCode = $this->commissionHelper->getAreaCode();
        if($ownerUserId != self::ADMIN_USER && $areaCode == 'adminhtml'):
            parent::_initSelect();
            $this->addFieldToFilter('user_id', $ownerUserId);
            return $this;
        else:
            parent::_initSelect();
        endif;
    }

    /**
     * Add field filter to collection
     *
     * @param string|array $field
     * @param null|string|array $condition
     * @return $this
     */
    public function addFieldToFilter($field, $condition = null)
    {
        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Add "include in recent" filter to collection
     * @return $this
     */
    public function addRecentFilter()
    {
      return $this->addFieldToFilter('include_in_recent', 1);
    }
    
    /**
     * Add Redemption filter to collection
     * @param array|int|string  $redemption
     * @return $this
     */
    public function addRedemptionsFilter($redemptionIds)
    {
        if (!is_array($redemptionIds)) {
            $redemptionIds = explode(',', $redemptionIds);
            foreach ($redemptionIds as $key => $id) {
                $id = trim($id);
                if (!$id) {
                    unset($redemptionIds[$key]);
                }
            }
        }

        if (!count($redemptionIds)) {
            $redemptionIds = [0];
        }

        $this->addFieldToFilter(
            'entity_id',
            ['in' => $redemptionIds]
        );
    }

    /**
     * Add is_active filter to collection
     * @return $this
     */
    public function addActiveFilter()
    {
        return $this->addFieldToFilter('is_active', 1);
    }

    /**
     * Get SQL for get record count
     *
     * Extra GROUP BY strip added.
     *
     * @return \Magento\Framework\DB\Select
     */
    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(\Magento\Framework\DB\Select::GROUP);

        return $countSelect;
    }
}
