<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Block\Adminhtml\Order\View\Tab;

use Designnbuy\OrderTicket\Model\ResourceModel\OrderTicket\Collection;
use Designnbuy\OrderTicket\Model\ResourceModel\OrderTicket\Status\History\CollectionFactory as HistoryCollectionFactory;
use Designnbuy\OrderTicket\Model\OrderTicket\Source\Status;
use Designnbuy\OrderTicket\Model\OrderTicket\Status\History as StatusHistory;
use Magento\Sales\Block\Adminhtml\Order\View\Tab\History;

/**
 * Class HistoryPlugin
 * @package Designnbuy\OrderTicket\Block\Adminhtml\Order\View\Tab
 */
class HistoryPlugin
{
    /**
     * @var Collection
     */
    private $orderticketCollection;

    /**
     * @var HistoryCollectionFactory
     */
    private $historyCollectionFactory;

    /**
     * @param Collection $orderticketCollection
     * @param HistoryCollectionFactory $historyCollectionFactory
     */
    public function __construct(Collection $orderticketCollection, HistoryCollectionFactory $historyCollectionFactory)
    {
        $this->orderticketCollection = $orderticketCollection;
        $this->historyCollectionFactory = $historyCollectionFactory;
    }

    /**
     * Add Returns to Order Comments history
     *
     * @param History $subject
     * @param array $history
     * @return array
     */
    public function afterGetFullHistory(History $subject, array $history)
    {
        $collection = $this->orderticketCollection->addFieldToFilter('order_id', $subject->getOrder()->getId())->load();
        $creationSystemComment = StatusHistory::getSystemCommentByStatus(Status::STATE_PENDING);
        /** @var $historyCollection \Designnbuy\OrderTicket\Model\ResourceModel\OrderTicket\Status\History\Collection */
        $historyCollection = $this->historyCollectionFactory->create();
        /** @var \Designnbuy\OrderTicket\Model\OrderTicket $orderticket */
        foreach ($collection as $orderticket) {
            /** @var $comments \Designnbuy\OrderTicket\Model\OrderTicket\Status\History[] */
            $comments = $historyCollection->getItemsByColumnValue('orderticket_entity_id', $orderticket->getId());
            foreach ($comments as $comment) {
                if ($comment->getComment() == $creationSystemComment) {
                    $history[] = [
                        'title' => sprintf('Return #%s created', $orderticket->getIncrementId()),
                        'notified' => $comment->getIsCustomerNotified(),
                        'comment' => '',
                        'created_at' => $comment->getCreatedAtDate(),
                    ];
                }
            }
        }
        usort($history, [get_class($subject), 'sortHistoryByTimestamp']);
        return $history;
    }
}
