<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Controller\Returns;

use Designnbuy\OrderTicket\Model\OrderTicket;

class View extends \Designnbuy\OrderTicket\Controller\Returns
{
    /**
     * ORDERTICKET view page
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->_loadValidOrderTicket()) {
            $this->_redirect('*/*/history');
            return;
        }
        /** @var $order \Magento\Sales\Model\Order */
        $order = $this->_objectManager->create(
            'Magento\Sales\Model\Order'
        )->load(
            $this->_coreRegistry->registry('current_orderticket')->getOrderId()
        );
        $this->_coreRegistry->register('current_order', $order);

        $this->_view->loadLayout();
        $this->_view->getPage()->getConfig()->getTitle()->set(
            __('Order Ticket #%1', $this->_coreRegistry->registry('current_orderticket')->getIncrementId())
        );

        $this->_view->renderLayout();
    }
}
