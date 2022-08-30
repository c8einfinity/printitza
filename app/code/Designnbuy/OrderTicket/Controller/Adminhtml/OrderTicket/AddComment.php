<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Controller\Adminhtml\OrderTicket;

class AddComment extends \Designnbuy\OrderTicket\Controller\Adminhtml\OrderTicket
{
    /**
     * Add ORDERTICKET comment action
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    public function execute()
    {
        try {
            $this->_initModel();
            $data = $this->getRequest()->getPost('comment');
            $notify = isset($data['is_customer_notified']);
            $visible = isset($data['is_visible_on_front']);

            $orderticket = $this->_coreRegistry->registry('current_orderticket');
            if (!$orderticket) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Invalid Order Ticket'));
            }

            $model = $this->_objectManager->create('Designnbuy\OrderTicket\Model\OrderTicket');
            $model->load($orderticket->getId());
            $model->setStatus($data['status']);
            $model->save();

            $comment = trim($data['comment']);

            if (!$comment) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Please enter a valid message.'));
            }
            /** @var $history \Designnbuy\OrderTicket\Model\OrderTicket\Status\History */
            $history = $this->_objectManager->create('Designnbuy\OrderTicket\Model\OrderTicket\Status\History');
            $history->setOrderTicketEntityId($orderticket->getId());
            $history->setComment($comment);
            if(isset($data) && !empty($data['file'])){
                $file = $data['file'];
                $history->setFile($file);
            }

            if ($notify) {
                $history->sendCommentEmail();
            }
            $history->saveComment($comment, $visible, true);

            $this->_view->loadLayout();
            $response = $this->_view->getLayout()->getBlock('comments_history')->toHtml();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $response = ['error' => true, 'message' => $e->getMessage()];
        } catch (\Exception $e) {
            $response = ['error' => true, 'message' => __('We cannot add the Order Ticket history.')];
        }
        if (is_array($response)) {
            $this->getResponse()->representJson(
                $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($response)
            );
        } else {
            $this->getResponse()->setBody($response);
        }
    }
}
