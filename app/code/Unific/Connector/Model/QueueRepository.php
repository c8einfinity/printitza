<?php

namespace Unific\Connector\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Unific\Connector\Api\Data\QueueInterface;
use Unific\Connector\Api\QueueRepositoryInterface;

class QueueRepository implements QueueRepositoryInterface
{
    /**
     * @var QueueFactory
     */
    private $queueFactory;

    private $queueCollectionFactory;

    /**
     * QueueRepository constructor.
     * @param QueueFactory $queueFactory
     * @param ResourceModel\Queue\CollectionFactory $queueCollectionFactory
     */
    public function __construct(
        QueueFactory $queueFactory,
        ResourceModel\Queue\CollectionFactory $queueCollectionFactory
    ) {
        $this->queueFactory = $queueFactory;
        $this->queueCollectionFactory = $queueCollectionFactory;
    }

    /**
     * @param $id
     * @return QueueInterface
     */
    public function getById($id)
    {
        $queue = $this->hamburgerFactory->create();
        $queue->getResource()->load($queue, $id);
        if (!$queue->getId()) {
            throw new NoSuchEntityException(__('Unable to find queue with GUID "%1"', $id));
        }
        return $queue;
    }

    /**
     * @param QueueInterface $queue
     * @return QueueInterface
     */
    public function save(QueueInterface $queue)
    {
        $queue->getResource()->save($queue);
        return $queue;
    }

    /**
     * @param QueueInterface $queue
     */
    public function delete(QueueInterface $queue)
    {
        $queue->getResource()->delete($queue);
    }

    /**
     * @param $id
     * @return bool
     */
    public function deleteById($id)
    {
        $queue = $this->hamburgerFactory->create();
        $queue->getResource()->load($queue, $id);
        if (!$queue->getId()) {
            throw new NoSuchEntityException(__('Unable to find queue with GUID "%1"', $id));
        } else {
            $queue->delete($queue);
        }

        return $queue;
    }

    /**
     * @return bool
     */
    public function truncateQueue()
    {
        $this->queueCollectionFactory->create()->walk('delete');

        return true;
    }

    /**
     * @return bool
     */
    public function truncateHistorical()
    {
        $this->queueCollectionFactory->create()
            ->addFieldToFilter('headers', ['like' => '%/historical%'])->walk('delete');

        return true;
    }
}
