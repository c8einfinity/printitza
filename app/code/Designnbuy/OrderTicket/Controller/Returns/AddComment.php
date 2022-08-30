<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Controller\Returns;

use Designnbuy\OrderTicket\Model\OrderTicket;
use Magento\Framework\Controller\ResultFactory;

class AddComment extends \Designnbuy\OrderTicket\Controller\Returns
{
    /**
     * Add ORDERTICKET comment action
     *
     * @return void|\Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        if (!$this->_loadValidOrderTicket()) {
            return;
        }
        try {

            $data = $this->getRequest()->getPost('comment');

            $comment = trim(strip_tags($data['comment']));
            $file = '';
            if(isset($data) && !empty($data['file'])){
                $file = $data['file'];
            }

            if (empty($comment)) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Please enter a valid message.'));
            }
            /** @var $statusHistory \Designnbuy\OrderTicket\Model\OrderTicket\Status\History */
            $statusHistory = $this->_objectManager->create('Designnbuy\OrderTicket\Model\OrderTicket\Status\History');
            $orderticket = $this->_coreRegistry->registry('current_orderticket');
            $statusHistory->setOrderTicketEntityId($orderticket->getId());
            $statusHistory->setComment($comment);
            $statusHistory->setFile($file);
            $statusHistory->sendCustomerCommentEmail();
            $statusHistory->saveComment($comment, true, false);
            $this->messageManager->addSuccess(__('You submitted Order Ticket #%1.', $orderticket->getIncrementId()));
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError(__('Cannot add message.'));
        }

        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/view', ['entity_id' => (int) $this->getRequest()->getParam('entity_id')]);
    }
}
