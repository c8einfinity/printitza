<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Controller\Returns;

class Returns extends \Designnbuy\OrderTicket\Controller\Returns
{
    /**
     * View ORDERTICKET for Order
     *
     * @return false|null
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function execute()
    {
        $orderId = (int)$this->getRequest()->getParam('order_id');
        $customerId = $this->_objectManager->get('Magento\Customer\Model\Session')->getCustomerId();

        if (!$orderId || !$this->_isEnabledOnFront()) {
            $this->_forward('noroute');
            return false;
        }

        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($orderId);

        $availableStatuses = $this->_objectManager
            ->get('Magento\Sales\Model\Order\Config')
            ->getVisibleOnFrontStatuses();
        if ($order->getId() && $order->getCustomerId() && $order->getCustomerId() == $customerId && in_array(
            $order->getStatus(),
            $availableStatuses,
            $strict = true
        )
        ) {
            $this->_coreRegistry->register('current_order', $order);
        } else {
            $this->_redirect('*/*/history');
            return;
        }

        $this->_view->loadLayout();
        $layout = $this->_view->getLayout();

        if ($navigationBlock = $layout->getBlock('customer_account_navigation')) {
            $navigationBlock->setActive('sales/order/history');
        }
        $this->_view->renderLayout();
    }
}
