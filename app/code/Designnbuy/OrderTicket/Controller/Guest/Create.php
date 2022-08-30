<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Controller\Guest;

use Designnbuy\OrderTicket\Model\OrderTicket;

class Create extends \Designnbuy\OrderTicket\Controller\Guest
{
    /**
     * Try to load valid collection of ordered items
     *
     * @param int $orderId
     * @return bool
     */
    protected function _loadOrderItems($orderId)
    {
        if ($this->orderticketHelper->canCreateOrderTicket($orderId)) {
            return true;
        }

        $incrementId = $this->_coreRegistry->registry('current_order')->getIncrementId();
        $message = __('We can\'t create a return transaction for order #%1.', $incrementId);
        $this->messageManager->addError($message);
        return false;
    }

    /**
     * Customer create new return
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        $result = $this->salesGuestHelper->loadValidOrder($this->_request);
        if ($result instanceof \Magento\Framework\Controller\ResultInterface) {
            return $result;
        }
        $order = $this->_coreRegistry->registry('current_order');
        $orderId = $order->getId();
        if (!$this->_loadOrderItems($orderId)) {
            return $this->resultRedirectFactory->create()->setPath('sales/order/history');
        }

        $post = $this->getRequest()->getPostValue();
        /** @var \Magento\Framework\Stdlib\DateTime\DateTime $coreDate */
        $coreDate = $this->_objectManager->get('Magento\Framework\Stdlib\DateTime\DateTime');
        if ($post && !empty($post['items'])) {
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
                $result = $orderticketModel->setData($orderticketData)->saveOrderTicket($post);

                if (!$result) {
                    return $this->resultRedirectFactory->create()->setPath('*/*/create', ['order_id' => $orderId]);
                }
                /** @var $statusHistory \Designnbuy\OrderTicket\Model\OrderTicket\Status\History */
                $statusHistory = $this->_objectManager->create('Designnbuy\OrderTicket\Model\OrderTicket\Status\History');
                $statusHistory->setOrderTicketEntityId($result->getId());
                $statusHistory->sendNewOrderTicketEmail();
                $statusHistory->saveSystemComment();
                if (isset($post['orderticket_comment']) && !empty($post['orderticket_comment'])) {
                    /** @var $statusHistory \Designnbuy\OrderTicket\Model\OrderTicket\Status\History */
                    $comment = $this->_objectManager->create('Designnbuy\OrderTicket\Model\OrderTicket\Status\History');
                    $comment->setOrderTicketEntityId($result->getId());
                    $comment->saveComment($post['orderticket_comment'], true, false);
                }
                $this->messageManager->addSuccess(__('You submitted Return #%1.', $orderticketModel->getIncrementId()));
                $url = $this->_url->getUrl('*/*/returns');
                return $this->resultRedirectFactory->create()->setUrl($this->_redirect->success($url));
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('We can\'t create a return right now. Please try again later.')
                );
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            }
        }
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Create New Order Ticket'));
        if ($block = $resultPage->getLayout()->getBlock('customer.account.link.back')) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }
        return $resultPage;
    }
}
