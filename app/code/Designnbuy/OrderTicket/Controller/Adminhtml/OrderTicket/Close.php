<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Controller\Adminhtml\OrderTicket;

class Close extends \Designnbuy\OrderTicket\Controller\Adminhtml\OrderTicket
{
    /**
     * Close action for orderticket
     *
     * @return void
     */
    public function execute()
    {
        $entityId = $this->getRequest()->getParam('entity_id');
        if ($entityId) {
            $entityId = intval($entityId);
            $entityIds = [$entityId];
            $returnOrderTicket = $entityId;
        } else {
            $entityIds = $this->getRequest()->getPost('entity_ids', []);
            $returnOrderTicket = null;
        }
        $countCloseOrderTicket = 0;
        $countNonCloseOrderTicket = 0;
        foreach ($entityIds as $entityId) {
            /** @var $orderticket \Designnbuy\OrderTicket\Model\OrderTicket */
            $orderticket = $this->_objectManager->create('Designnbuy\OrderTicket\Model\OrderTicket')->load($entityId);
            if ($orderticket->canClose()) {
                $orderticket->close()->save();
                /** @var $statusHistory \Designnbuy\OrderTicket\Model\OrderTicket\Status\History */
                $statusHistory = $this->_objectManager->create('Designnbuy\OrderTicket\Model\OrderTicket\Status\History');
                $statusHistory->setOrderTicketEntityId($orderticket->getId());
                $statusHistory->saveSystemComment();
                $countCloseOrderTicket++;
            } else {
                $countNonCloseOrderTicket++;
            }
        }
        if ($countNonCloseOrderTicket) {
            if ($countCloseOrderTicket) {
                $this->messageManager->addError(__('%1 Order Ticket(s) cannot be closed', $countNonCloseOrderTicket));
            } else {
                $this->messageManager->addError(__('We cannot close the Order Ticket request(s).'));
            }
        }
        if ($countCloseOrderTicket) {
            $this->messageManager->addSuccess(__('%1 Order Ticket (s) have been closed.', $countCloseOrderTicket));
        }

        if ($returnOrderTicket) {
            $this->_redirect('adminhtml/*/edit', ['id' => $returnOrderTicket]);
        } else {
            $this->_redirect('adminhtml/*/');
        }
    }
}
