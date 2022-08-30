<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Controller\Adminhtml\OrderTicket;

class SaveNew extends \Designnbuy\OrderTicket\Controller\Adminhtml\OrderTicket
{
    /**
     * Process additional ORDERTICKET infoordertickettion (like comment, customer notification etc)
     *
     * @param array $saveRequest
     * @param \Designnbuy\OrderTicket\Model\OrderTicket $orderticket
     * @return \Designnbuy\OrderTicket\Controller\Adminhtml\OrderTicket
     */
    protected function _processNewOrderTicketAdditionalInfo(array $saveRequest, \Designnbuy\OrderTicket\Model\OrderTicket $orderticket)
    {
        /** @var $statusHistory \Designnbuy\OrderTicket\Model\OrderTicket\Status\History */
        $systemComment = $this->_objectManager->create('Designnbuy\OrderTicket\Model\OrderTicket\Status\History');
        $systemComment->setOrderTicketEntityId($orderticket->getEntityId());
        if (isset($saveRequest['orderticket_confiordertickettion']) && $saveRequest['orderticket_confiordertickettion']) {
            try {
                $systemComment->sendNewOrderTicketEmail();
            } catch (\Magento\Framework\Exception\MailException $exception) {
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($exception);
                $this->messageManager->addWarning(
                    __('You did not email your customer. Please check your email settings.')
                );
            }
        }
        $systemComment->saveSystemComment();

        if (!empty($saveRequest['comment']['comment'])) {
            $visible = isset($saveRequest['comment']['is_visible_on_front']);
            /** @var $statusHistory \Designnbuy\OrderTicket\Model\OrderTicket\Status\History */
            $customComment = $this->_objectManager->create('Designnbuy\OrderTicket\Model\OrderTicket\Status\History');
            $customComment->setOrderTicketEntityId($orderticket->getEntityId());
            if (!empty($saveRequest['comment']['file'])) {
                $customComment->setFile($saveRequest['comment']['file']);
            }
            if (!empty($saveRequest['comment']['is_customer_notified'])) {
                $customComment->setIsCustomerNotified($saveRequest['comment']['is_customer_notified']);
            }
            $notify = isset($data['is_customer_notified']);
            $customComment->saveComment($saveRequest['comment']['comment'], $visible, true);
        }


        return $this;
    }

    /**
     * Save new ORDERTICKET request
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        if (!$this->getRequest()->isPost() || $this->getRequest()->getParam('back', false)) {
            $this->_redirect('adminhtml/*/');
            return;
        }
        try {
            /** @var $model \Designnbuy\OrderTicket\Model\OrderTicket */
            $model = $this->_initModel();
            //$saveRequest = $this->orderticketDataMapper->filterOrderTicketSaveRequest($this->getRequest()->getPostValue());
            $saveRequest = $this->getRequest()->getPostValue();
            $model->setData(
                $this->orderticketDataMapper->prepareNewOrderTicketInstanceData(
                    $saveRequest,
                    $this->_coreRegistry->registry('current_order')
                )
            );
            if (!$model->saveOrderTicket($saveRequest)) {
                //throw new \Magento\Framework\Exception\LocalizedException(__('We can\'t save this ORDERTICKET.'));
            }
            $this->_processNewOrderTicketAdditionalInfo($saveRequest, $model);
            $this->messageManager->addSuccess(__('You submitted the Order Ticket request.'));
        } catch (\Magento\Framework\Exception\LocalizedException $e) {

            $this->messageManager->addError($e->getMessage());
            $errorKeys = $this->_objectManager->get('Magento\Framework\Session\Generic')->getOrderTicketErrorKeys();
            $controllerParams = ['order_id' => $this->_coreRegistry->registry('current_order')->getId()];
            if (!empty($errorKeys) && isset($errorKeys['tabs']) && $errorKeys['tabs'] == 'items_section') {
                $controllerParams['active_tab'] = 'items_section';
            }
            $this->_redirect('adminhtml/*/new', $controllerParams);
            return;
        } catch (\Exception $e) {

            $this->messageManager->addError(__('We can\'t save this Order Ticket.'));
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
        }
        $this->_redirect('adminhtml/*/');
    }
}
