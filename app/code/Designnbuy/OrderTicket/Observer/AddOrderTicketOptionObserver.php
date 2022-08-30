<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

/**
 * ORDERTICKET observer
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class AddOrderTicketOptionObserver implements ObserverInterface
{
    /**
     * OrderTicket data
     *
     * @var \Designnbuy\OrderTicket\Helper\Data
     */
    protected $_orderticketData = null;

    /**
     * @param \Designnbuy\OrderTicket\Helper\Data $orderticketData
     */
    public function __construct(\Designnbuy\OrderTicket\Helper\Data $orderticketData)
    {
        $this->_orderticketData = $orderticketData;
    }

    /**
     * Add orderticket availability option to options column in customer's order grid
     *
     * @param EventObserver $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        $renderer = $observer->getEvent()->getRenderer();
        /** @var $row \Magento\Sales\Model\Order */
        $row = $observer->getEvent()->getRow();

        if ($this->_orderticketData->canCreateOrderTicket($row, true)) {
            $reorderAction = [
                '@' => ['href' => $renderer->getUrl('*/orderticket/new', ['order_id' => $row->getId()])],
                '#' => __('Order Ticket'),
            ];
            $renderer->addToActions($reorderAction);
        }
    }
}
