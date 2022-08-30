<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Customer\Controller\Adminhtml\Queue;

class Start extends \Designnbuy\Customer\Controller\Adminhtml\Queue
{
    /**
     * Start Customer queue
     *
     * @return void
     */
    public function execute()
    {
        $queue = $this->_objectManager->create(
            'Designnbuy\Customer\Model\Queue'
        )->load(
            $this->getRequest()->getParam('id')
        );
        if ($queue->getId()) {
            if (!in_array(
                $queue->getQueueStatus(),
                [\Designnbuy\Customer\Model\Queue::STATUS_NEVER, \Designnbuy\Customer\Model\Queue::STATUS_PAUSE]
            )
            ) {
                $this->_redirect('*/*');
                return;
            }

            $queue->setQueueStartAt(
                $this->_objectManager->get('Magento\Framework\Stdlib\DateTime\DateTime')->gmtDate()
            )->setQueueStatus(
                \Designnbuy\Customer\Model\Queue::STATUS_SENDING
            )->save();
        }

        $this->_redirect('*/*');
    }
}
