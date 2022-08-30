<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Controller\Adminhtml\OrderTicket;

class OrderTicketOrder extends \Designnbuy\OrderTicket\Controller\Adminhtml\OrderTicket
{
    /**
     * Generate ORDERTICKET grid for ajax request from order page
     *
     * @return void
     */
    public function execute()
    {
        $orderId = intval($this->getRequest()->getParam('order_id'));
        $this->getResponse()->setBody(
            $this->_view->getLayout()->createBlock(
                'Designnbuy\OrderTicket\Block\Adminhtml\Order\View\Tab\OrderTicket'
            )->setOrderId(
                $orderId
            )->toHtml()
        );
    }
}
