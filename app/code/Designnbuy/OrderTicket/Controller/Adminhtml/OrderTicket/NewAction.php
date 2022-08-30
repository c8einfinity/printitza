<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Controller\Adminhtml\OrderTicket;

class NewAction extends \Designnbuy\OrderTicket\Controller\Adminhtml\OrderTicket
{
    /**
     * Create new ORDERTICKET
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        if (!$orderId) {
            $customerId = $this->getRequest()->getParam('customer_id');
            $this->_redirect('adminhtml/*/chooseorder', ['customer_id' => $customerId]);
        } else {
            try {
                $this->_initCreateModel();
                $this->_initModel();
                
                if (!$this->_objectManager->get('Designnbuy\OrderTicket\Helper\Data')->canCreateOrderTicket($orderId, true)) {
                    $this->messageManager->addError(__('There are no applicable items for return in this order.'));
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_redirect('adminhtml/*/');
                return;
            }

            $this->_initAction();
            $this->_view->getPage()->getConfig()->getTitle()->prepend(__('New Order Ticket'));
            $this->_view->renderLayout();
        }
    }
}
