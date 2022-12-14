<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Customer\Model\ResourceModel\Queue;

/**
 * Customer queue collection.
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * True when designs info joined
     *
     * @var bool
     */
    protected $_addDesignsFlag = false;

    /**
     * True when filtered by store
     *
     * @var bool
     */
    protected $_isStoreFilter = false;

    /**
     * Date
     *
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\DB\Adapter\AdapterInterface $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->_date = $date;
    }

    /**
     * Initializes collection
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_map['fields']['queue_id'] = 'main_table.queue_id';
        $this->_init('Designnbuy\Customer\Model\Queue', 'Designnbuy\Customer\Model\ResourceModel\Queue');
    }

    /**
     * Joines templates information
     *
     * @return $this
     */
    public function addTemplateInfo()
    {
        $this->getSelect()->joinLeft(
            ['template' => $this->getTable('customer_template')],
            'template.template_id=main_table.template_id',
            ['template_subject', 'template_sender_name', 'template_sender_email']
        );
        $this->_joinedTables['template'] = true;
        return $this;
    }

    /**
     * Adds designs info to selelect
     *
     * @return $this
     */
    protected function _addDesignInfoToSelect()
    {
        /** @var $select \Magento\Framework\DB\Select */
        $select = $this->getConnection()->select()->from(
            ['qlt' => $this->getTable('designnbuy_customer_queue_link')],
            'COUNT(qlt.queue_link_id)'
        )->where(
            'qlt.queue_id = main_table.queue_id'
        );
        $totalExpr = new \Zend_Db_Expr(sprintf('(%s)', $select->assemble()));
        $select = $this->getConnection()->select()->from(
            ['qls' => $this->getTable('designnbuy_customer_queue_link')],
            'COUNT(qls.queue_link_id)'
        )->where(
            'qls.queue_id = main_table.queue_id'
        )->where(
            'qls.letter_sent_at IS NOT NULL'
        );
        $sentExpr = new \Zend_Db_Expr(sprintf('(%s)', $select->assemble()));

        $this->getSelect()->columns(['designs_sent' => $sentExpr, 'designs_total' => $totalExpr]);
        return $this;
    }

    /**
     * Adds designs info to select and loads collection
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @return $this
     */
    public function load($printQuery = false, $logQuery = false)
    {
        if ($this->_addDesignsFlag && !$this->isLoaded()) {
            $this->_addDesignInfoToSelect();
        }
        return parent::load($printQuery, $logQuery);
    }

    /**
     * Joines designs information
     *
     * @return $this
     */
    public function addDesignsInfo()
    {
        $this->_addDesignsFlag = true;
        return $this;
    }

    /**
     * Checks if field is 'designs_total', 'designs_sent'
     * to add specific filter or adds reguler filter
     *
     * @param string $field
     * @param null|string|array $condition
     * @return $this
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if (in_array($field, ['designs_total', 'designs_sent'])) {
            $this->addFieldToFilter('main_table.queue_id', ['in' => $this->_getIdsFromLink($field, $condition)]);
            return $this;
        } else {
            return parent::addFieldToFilter($field, $condition);
        }
    }

    /**
     * Returns ids from queue_link table
     *
     * @param string $field
     * @param null|string|array $condition
     * @return array
     */
    protected function _getIdsFromLink($field, $condition)
    {
        $select = $this->getConnection()->select()->from(
            $this->getTable('designnbuy_customer_queue_link'),
            ['queue_id', 'total' => new \Zend_Db_Expr('COUNT(queue_link_id)')]
        )->group(
            'queue_id'
        )->having(
            $this->_getConditionSql('total', $condition)
        );

        if ($field == 'designs_sent') {
            $select->where('letter_sent_at IS NOT NULL');
        }

        $idList = $this->getConnection()->fetchCol($select);

        if (count($idList)) {
            return $idList;
        }

        return [0];
    }

    /**
     * Set filter for queue by design.
     *
     * @param int $designId
     * @return $this
     */
    public function addDesignFilter($designId)
    {
        $this->getSelect()->join(
            ['link' => $this->getTable('designnbuy_customer_queue_link')],
            'main_table.queue_id=link.queue_id',
            ['letter_sent_at']
        )->where(
            'link.design_id = ?',
            $designId
        );

        return $this;
    }

    /**
     * Add filter by only ready fot sending item
     *
     * @return $this
     */
    public function addOnlyForSendingFilter()
    {
        $this->getSelect()->where(
            'main_table.queue_status in (?)',
            [\Designnbuy\Customer\Model\Queue::STATUS_SENDING, \Designnbuy\Customer\Model\Queue::STATUS_NEVER]
        )->where(
            'main_table.queue_start_at < ?',
            $this->_date->gmtdate()
        )->where(
            'main_table.queue_start_at IS NOT NULL'
        );

        return $this;
    }

    /**
     * Add filter by only not sent items
     *
     * @return $this
     */
    public function addOnlyUnsentFilter()
    {
        $this->addFieldToFilter('main_table.queue_status', \Designnbuy\Customer\Model\Queue::STATUS_NEVER);

        return $this;
    }

    /**
     * Returns options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('queue_id', 'template_subject');
    }

    /**
     * Filter collection by specified store ids
     *
     * @param int[]|int $storeIds
     * @return $this
     */
    public function addStoreFilter($storeIds)
    {
        if (!$this->_isStoreFilter) {
            $this->getSelect()->joinInner(
                ['store_link' => $this->getTable('designnbuy_customer_queue_store_link')],
                'main_table.queue_id = store_link.queue_id',
                []
            )->where(
                'store_link.store_id IN (?)',
                $storeIds
            )->group(
                'main_table.queue_id'
            );
            $this->_isStoreFilter = true;
        }
        return $this;
    }
}
