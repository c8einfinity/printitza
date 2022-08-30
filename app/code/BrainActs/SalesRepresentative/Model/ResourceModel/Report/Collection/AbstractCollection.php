<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Model\ResourceModel\Report\Collection;

/**
 * Class AbstractCollection
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class AbstractCollection extends \Magento\Reports\Model\ResourceModel\Report\Collection\AbstractCollection
{
    /**
     * Order status
     *
     * @var string
     */
    protected $_member = null;//@codingStandardsIgnoreLine

    /**
     * AbstractCollection constructor.
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Sales\Model\ResourceModel\Report $resource
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $connection
     */
    public function __construct(//@codingStandardsIgnoreLine
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Sales\Model\ResourceModel\Report $resource,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->setModel(\Magento\Reports\Model\Item::class);
    }

    /**
     * Set status filter
     *
     * @param $member
     * @return $this
     */
    public function addMemberFilter($member)
    {
        $this->_member = $member;
        return $this;
    }

    /**
     * Apply order status filter
     *
     * @return $this
     */
    public function _applyMemberFilter()
    {
        if ($this->_member === null) {
            return $this;
        }
        $member = $this->_member;
        if (!is_array($member)) {
            $member = [$member];
        }
        $this->getSelect()->where($this->getMainTable() . '.member_id IN(?)', $member);
        return $this;
    }

    /**
     * Order status filter is custom for this collection
     *
     * @return $this
     */
    protected function _applyCustomFilter()//@codingStandardsIgnoreLine
    {
        return $this->_applyMemberFilter();
    }
}
