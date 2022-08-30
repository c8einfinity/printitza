<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Designnbuy\Customer\Controller\Adminhtml\Queue;

class Save extends \Designnbuy\Customer\Controller\Adminhtml\Queue
{
    /**
     * Save Customer queue
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        try {
            /* @var $queue \Designnbuy\Customer\Model\Queue */
            $queue = $this->_objectManager->create('Designnbuy\Customer\Model\Queue');

            $templateId = $this->getRequest()->getParam('template_id');
            if ($templateId) {
                /* @var $template \Designnbuy\Customer\Model\Template */
                $template = $this->_objectManager->create('Designnbuy\Customer\Model\Template')->load($templateId);

                if (!$template->getId() || $template->getIsSystem()) {
                    throw new \Magento\Framework\Exception\LocalizedException(__('Please correct the customer template and try again.'));
                }

                $queue->setTemplateId(
                    $template->getId()
                )->setQueueStatus(
                    \Designnbuy\Customer\Model\Queue::STATUS_NEVER
                );
            } else {
                $queue->load($this->getRequest()->getParam('id'));
            }

            if (!in_array(
                $queue->getQueueStatus(),
                [\Designnbuy\Customer\Model\Queue::STATUS_NEVER, \Designnbuy\Customer\Model\Queue::STATUS_PAUSE]
            )
            ) {
                $this->_redirect('*/*');
                return;
            }

            if ($queue->getQueueStatus() == \Designnbuy\Customer\Model\Queue::STATUS_NEVER) {
                $queue->setQueueStartAtByString($this->getRequest()->getParam('start_at'));
            }

            $queue->setStores(
                $this->getRequest()->getParam('stores', [])
            )->setCustomerSubject(
                $this->getRequest()->getParam('subject')
            )->setCustomerSenderName(
                $this->getRequest()->getParam('sender_name')
            )->setCustomerSenderEmail(
                $this->getRequest()->getParam('sender_email')
            )->setCustomerText(
                $this->getRequest()->getParam('text')
            )->setCustomerStyles(
                $this->getRequest()->getParam('styles')
            );

            if ($queue->getQueueStatus() == \Designnbuy\Customer\Model\Queue::STATUS_PAUSE
                && $this->getRequest()->getParam(
                    '_resume',
                    false
                )
            ) {
                $queue->setQueueStatus(\Designnbuy\Customer\Model\Queue::STATUS_SENDING);
            }

            $queue->save();

            $this->messageManager->addSuccess(__('You saved the customer queue.'));
            $this->_getSession()->setFormData(false);
            $this->_getSession()->unsPreviewData();

            $this->_redirect('*/*');
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $this->_redirect('*/*/edit', ['id' => $id]);
            } else {
                $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl($this->getUrl('*')));
            }
        }
    }
}
