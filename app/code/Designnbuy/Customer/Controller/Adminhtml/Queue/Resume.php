<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Customer\Controller\Adminhtml\Queue;

class Resume extends \Designnbuy\Customer\Controller\Adminhtml\Queue
{
    /**
     * Resume Customer queue
     *
     * @return void
     */
    public function execute()
    {
        $queue = $this->_objectManager->get(
            'Designnbuy\Customer\Model\Queue'
        )->load(
            $this->getRequest()->getParam('id')
        );

        if (!in_array($queue->getQueueStatus(), [\Designnbuy\Customer\Model\Queue::STATUS_PAUSE])) {
            $this->_redirect('*/*');
            return;
        }

        $queue->setQueueStatus(\Designnbuy\Customer\Model\Queue::STATUS_SENDING);
        $queue->save();

        $this->_redirect('*/*');
    }
}
