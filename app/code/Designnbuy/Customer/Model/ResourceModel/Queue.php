<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Customer\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Designnbuy\Customer\Model\Queue as ModelQueue;

/**
 * Customer queue resource model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Queue extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Design collection
     *
     * @var \Designnbuy\Customer\Model\ResourceModel\Design\Collection
     */
    protected $_designCollection;

    /**
     * Construct
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Designnbuy\Customer\Model\ResourceModel\Design\Collection $designCollection
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Designnbuy\Customer\Model\ResourceModel\Design\Collection $designCollection,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->_designCollection = $designCollection;
    }

    /**
     * Define main table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('designnbuy_customer_queue', 'queue_id');
    }

    /**
     * Add designs to queue
     *
     * @param ModelQueue $queue
     * @param array $designIds
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addDesignsToQueue(ModelQueue $queue, array $designIds)
    {
        if (count($designIds) == 0) {
            throw new \Magento\Framework\Exception\LocalizedException(__('There are no designs selected.'));
        }

        if (!$queue->getId() && $queue->getQueueStatus() != \Designnbuy\Customer\Model\Queue::STATUS_NEVER) {
            throw new \Magento\Framework\Exception\LocalizedException(__('You selected an invalid queue.'));
        }

        $connection = $this->getConnection();

        $select = $connection->select();
        $select->from(
            $this->getTable('designnbuy_customer_queue_link'),
            'design_id'
        )->where(
            'queue_id = ?',
            $queue->getId()
        )->where(
            'design_id in (?)',
            $designIds
        );

        $usedIds = $connection->fetchCol($select);
        $connection->beginTransaction();
        try {
            foreach ($designIds as $designId) {
                if (in_array($designId, $usedIds)) {
                    continue;
                }
                $data = [];
                $data['queue_id'] = $queue->getId();
                $data['design_id'] = $designId;
                $connection->insert($this->getTable('designnbuy_customer_queue_link'), $data);
            }
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
        }
    }

    /**
     * Removes design from queue
     *
     * @param ModelQueue $queue
     * @return void
     * @throws \Exception
     */
    public function removeDesignsFromQueue(ModelQueue $queue)
    {
        $connection = $this->getConnection();
        try {
            $connection->beginTransaction();
            $connection->delete(
                $this->getTable('designnbuy_customer_queue_link'),
                ['queue_id = ?' => $queue->getId(), 'letter_sent_at IS NULL']
            );

            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            throw $e;
        }
    }

    /**
     * Links queue to store
     *
     * @param ModelQueue $queue
     * @return $this
     */
    public function setStores(ModelQueue $queue)
    {
        $connection = $this->getConnection();
        $connection->delete($this->getTable('designnbuy_customer_queue_store_link'), ['queue_id = ?' => $queue->getId()]);

        $stores = $queue->getStores();
        if (!is_array($stores)) {
            $stores = [];
        }

        foreach ($stores as $storeId) {
            $data = [];
            $data['store_id'] = $storeId;
            $data['queue_id'] = $queue->getId();
            $connection->insert($this->getTable('designnbuy_customer_queue_store_link'), $data);
        }
        $this->removeDesignsFromQueue($queue);

        if (count($stores) == 0) {
            return $this;
        }

        $designs = $this->_designCollection->addFieldToFilter(
            'store_id',
            ['in' => $stores]
        )->useOnlySubscribed()->load();

        $designIds = [];

        foreach ($designs as $design) {
            $designIds[] = $design->getId();
        }

        if (count($designIds) > 0) {
            $this->addDesignsToQueue($queue, $designIds);
        }

        return $this;
    }

    /**
     * Returns queue linked stores
     *
     * @param ModelQueue $queue
     * @return array
     */
    public function getStores(ModelQueue $queue)
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getTable('designnbuy_customer_queue_store_link'),
            'store_id'
        )->where(
            'queue_id = :queue_id'
        );

        if (!($result = $connection->fetchCol($select, ['queue_id' => $queue->getId()]))) {
            $result = [];
        }

        return $result;
    }

    /**
     * Saving template after saving queue action
     *
     * @param \Magento\Framework\Model\AbstractModel $queue
     * @return $this
     */
    protected function _afterSave(AbstractModel $queue)
    {
        if ($queue->getSaveStoresFlag()) {
            $this->setStores($queue);
        }
        return $this;
    }
}
