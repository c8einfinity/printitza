<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Controller\Guest;

use Designnbuy\OrderTicket\Model\OrderTicket;

class AddComment extends \Designnbuy\OrderTicket\Controller\Guest
{
    /**
     * Add ORDERTICKET comment action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $result = $this->_loadValidOrderTicket();
        if ($result instanceof \Magento\Framework\Controller\ResultInterface) {
            return $result;
        }

        try {
            $response = false;
            $comment = $this->getRequest()->getPost('comment');
            $comment = trim(strip_tags($comment));

            if (!empty($comment)) {
                /** @var $statusHistory \Designnbuy\OrderTicket\Model\OrderTicket\Status\History */
                $statusHistory = $this->_objectManager->create('Designnbuy\OrderTicket\Model\OrderTicket\Status\History');
                $statusHistory->setComment($comment);
                $orderticket = $this->_coreRegistry->registry('current_orderticket');
                $statusHistory->setOrderTicketEntityId($orderticket->getId());
                $statusHistory->sendCustomerCommentEmail();
                $statusHistory->saveComment($comment, true, false);
            } else {
                throw new \Magento\Framework\Exception\LocalizedException(__('Please enter a valid message.'));
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $response = ['error' => true, 'message' => $e->getMessage()];
        } catch (\Exception $e) {
            $response = ['error' => true, 'message' => __('We can\'t add a message right now.')];
        }
        if (is_array($response)) {
            $this->messageManager->addError($response['message']);
        }
        return $this->resultRedirectFactory->create()
            ->setPath('*/*/view', ['entity_id' => (int)$this->getRequest()->getParam('entity_id')]);
    }
}
