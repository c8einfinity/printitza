<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Designnbuy\OrderTicket\Model\OrderTicket;

class OrderTicketDataMapper
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTimeFactory
     */
    private $dateTimeFactory;


    /**
     * @param \Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateTimeFactory
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\DateTimeFactory $dateTimeFactory
    ) {
        $this->dateTimeFactory = $dateTimeFactory;
    }

    /**
     * Filter ORDERTICKET save request
     *
     * @param array $saveRequest
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function filterOrderTicketSaveRequest(array $saveRequest)
    {
        if (!isset($saveRequest['items'])) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('We can\'t save this Order Ticket. No items were specified.')
            );
        }
        $requiredKeys = ['qty_authorized', 'qty_approved', 'qty_returned', 'qty_requested'];
        $items = [];
        foreach ($saveRequest['items'] as $key => $itemData) {
            $intersection = array_intersect($requiredKeys, array_keys($itemData));
            if (empty($intersection)) {
                continue;
            }
            $itemData['entity_id'] = strpos($key, '_') === false ? $key : false;
            $items[$key] = $itemData;
        }
        $saveRequest['items'] = $items;
        return $saveRequest;
    }

    /**
     * Prepare ORDERTICKET instance data from save request
     *
     * @param array $saveRequest
     * @param \Magento\Sales\Model\Order $order
     * @return array
     */
    public function prepareNewOrderTicketInstanceData(array $saveRequest, \Magento\Sales\Model\Order $order)
    {
        /** @var $dateModel \Magento\Framework\Stdlib\DateTime\DateTime */
        $dateModel = $this->dateTimeFactory->create();
        $orderticketData = [
            'status' => \Designnbuy\OrderTicket\Model\OrderTicket\Source\Status::STATE_PENDING,
            'date_requested' => $dateModel->gmtDate(),
            'order_id' => $order->getId(),
            'job_id' => !empty($saveRequest['job_id']) ? $saveRequest['job_id'] : '',
            'order_increment_id' => $order->getIncrementId(),
            'store_id' => $order->getStoreId(),
            'customer_id' => $order->getCustomerId(),
            'order_date' => $order->getCreatedAt(),
            'customer_name' => $order->getCustomerName(),
            'customer_custom_email' => !empty($saveRequest['contact_email']) ? $saveRequest['contact_email'] : '',
        ];
        return $orderticketData;
    }

    /**
     * Combine item statuses from POST request items and original ORDERTICKET items
     *
     * @param array $requestedItems
     * @param int $orderticketId
     * @return array
     */
    public function combineItemStatuses(array $requestedItems, $orderticketId)
    {
        $statuses = [];
        foreach ($requestedItems as $requestedItem) {
            if (isset($requestedItem['status'])) {
                $statuses[] = $requestedItem['status'];
            }
        }

        return $statuses;
    }
}
