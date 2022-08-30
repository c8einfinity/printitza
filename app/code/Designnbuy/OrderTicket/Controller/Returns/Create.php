<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Controller\Returns;

use Designnbuy\OrderTicket\Model\OrderTicket;

class Create extends \Designnbuy\OrderTicket\Controller\Returns
{
    /**
     * Try to load valid collection of ordered items
     *
     * @param int $orderId
     * @return bool
     */
    protected function _loadOrderItems($orderId)
    {
        /** @var $orderticketHelper \Designnbuy\OrderTicket\Helper\Data */
        $orderticketHelper = $this->_objectManager->get('Designnbuy\OrderTicket\Helper\Data');
        if ($orderticketHelper->canCreateOrderTicket($orderId)) {
            return true;
        }

        $incrementId = $this->_coreRegistry->registry('current_order')->getIncrementId();
        $message = __('We can\'t create a return transaction for order #%1.', $incrementId);
        $this->messageManager->addError($message);
        $this->_redirect('sales/order/history');
        return false;
    }

    /**
     * Customer create new return
     *
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        $orderId = (int)$this->getRequest()->getParam('order_id');
        /** @var $order \Magento\Sales\Model\Order */
        $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($orderId);
        if (empty($orderId)) {
            $this->_redirect('sales/order/history');
            return;
        }
        $this->_coreRegistry->register('current_order', $order);

        if (!$this->_loadOrderItems($orderId)) {
            return;
        }

        /** @var \Magento\Framework\Stdlib\DateTime\DateTime $coreDate */
        $coreDate = $this->_objectManager->get('Magento\Framework\Stdlib\DateTime\DateTime');
        if (!$this->_canViewOrder($order)) {
            $this->_redirect('sales/order/history');
            return;
        }
        $post = $this->getRequest()->getPostValue();

        if ($post) {
            try {
                /** @var $orderticketModel \Designnbuy\OrderTicket\Model\OrderTicket */
                $orderticketModel = $this->_objectManager->create('Designnbuy\OrderTicket\Model\OrderTicket');
                $orderticketData = [
                    'status' => \Designnbuy\OrderTicket\Model\OrderTicket\Source\Status::STATE_PENDING,
                    'date_requested' => $coreDate->gmtDate(),
                    'order_id' => $order->getId(),
                    'order_increment_id' => $order->getIncrementId(),
                    'store_id' => $order->getStoreId(),
                    'customer_id' => $order->getCustomerId(),
                    'order_date' => $order->getCreatedAt(),
                    'customer_name' => $order->getCustomerName(),
                    'customer_custom_email' => $post['customer_custom_email'],
                ];
                if (!$orderticketModel->setData($orderticketData)->saveOrderTicket($post)) {
                    $url = $this->_url->getUrl('*/*/create', ['order_id' => $orderId]);
                    $this->getResponse()->setRedirect($this->_redirect->error($url));
                    return;
                }
                /** @var $statusHistory \Designnbuy\OrderTicket\Model\OrderTicket\Status\History */
                $statusHistory = $this->_objectManager->create('Designnbuy\OrderTicket\Model\OrderTicket\Status\History');
                $statusHistory->setOrderTicketEntityId($orderticketModel->getEntityId());
                $statusHistory->sendNewOrderTicketEmail();
                $statusHistory->saveSystemComment();
                $data = $post['comment'];
                if (isset($data['orderticket_comment']) && !empty($data['orderticket_comment'])) {
                    $comment = $this->_objectManager->create('Designnbuy\OrderTicket\Model\OrderTicket\Status\History');
                    $comment->setOrderTicketEntityId($orderticketModel->getEntityId());
                    if (isset($data['file']) && !empty($data['file'])) {
                        $comment->setFile($data['file']);
                    }
                    $comment->saveComment($data['orderticket_comment'], true, false);
                }
                $this->messageManager->addSuccess(__('You submitted Order Ticket #%1.', $orderticketModel->getIncrementId()));
                $this->getResponse()->setRedirect($this->_redirect->success($this->_url->getUrl('*/*/history')));
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('We can\'t create a order ticket right now. Please try again later.')
                );
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            }
        }
        $this->_view->loadLayout();
        $this->_view->getPage()->getConfig()->getTitle()->set(__('Create New Order Ticket'));
        if ($block = $this->_view->getLayout()->getBlock('customer.account.link.back')) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }
        $this->_view->renderLayout();
    }
}
