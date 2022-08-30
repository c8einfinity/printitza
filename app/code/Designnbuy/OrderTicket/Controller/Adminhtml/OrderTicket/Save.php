<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Controller\Adminhtml\OrderTicket;

class Save extends SaveNew
{
    /**
     * Save ORDERTICKET request
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        if (!$this->getRequest()->isPost()) {
            $this->_redirect('adminhtml/*/');
            return;
        }
        $orderticketId = (int)$this->getRequest()->getParam('orderticket_id');
        if (!$orderticketId) {
            parent::execute();
            return;
        }
        try {
            $saveRequest = $this->getRequest()->getPostValue();
            //$itemStatuses = $this->orderticketDataMapper->combineItemStatuses($saveRequest['items'], $orderticketId);
            $model = $this->_initModel('orderticket_id');
            /** @var $sourceStatus \Designnbuy\OrderTicket\Model\OrderTicket\Source\Status */
           // $sourceStatus = $this->_objectManager->create('Designnbuy\OrderTicket\Model\OrderTicket\Source\Status');
            //$model->setStatus($sourceStatus->getStatusByItems($itemStatuses))->setIsUpdate(1);
            if (!$model->saveOrderTicket($saveRequest)) {
                throw new \Magento\Framework\Exception\LocalizedException(__('We can\'t save this Order Ticket.'));
            }
            /** @var $statusHistory \Designnbuy\OrderTicket\Model\OrderTicket\Status\History */
            $statusHistory = $this->_objectManager->create('Designnbuy\OrderTicket\Model\OrderTicket\Status\History');
            $statusHistory->setOrderTicketEntityId($model->getEntityId());
            $statusHistory->sendAuthorizeEmail();
            $statusHistory->saveSystemComment();
            $this->messageManager->addSuccess(__('You saved the Order Ticket request.'));
            $redirectBack = $this->getRequest()->getParam('back', false);
            if ($redirectBack) {
                $this->_redirect('adminhtml/*/edit', ['id' => $orderticketId, 'store' => $model->getStoreId()]);
                return;
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
            $errorKeys = $this->_objectManager->get('Magento\Framework\Session\Generic')->getOrderTicketErrorKeys();
            $controllerParams = ['id' => $orderticketId];
            if (isset($errorKeys['tabs']) && $errorKeys['tabs'] == 'items_section') {
                $controllerParams['active_tab'] = 'items_section';
            }
            $this->_redirect('adminhtml/*/edit', $controllerParams);
            return;
        } catch (\Exception $e) {
            $this->messageManager->addError(__('We can\'t save this Order Ticket.'));
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            $this->_redirect('adminhtml/*/');
            return;
        }
        $this->_redirect('adminhtml/*/');
    }
}
