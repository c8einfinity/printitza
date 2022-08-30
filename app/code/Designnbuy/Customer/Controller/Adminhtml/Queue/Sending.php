<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Customer\Controller\Adminhtml\Queue;

class Sending extends \Designnbuy\Customer\Controller\Adminhtml\Queue
{
    /**
     * Send Customer queue
     *
     * @return void
     */
    public function execute()
    {
        // Todo: put it somewhere in config!
        $countOfQueue = 3;
        $countOfSubscritions = 20;

        $collection = $this->_objectManager->create(
            'Designnbuy\Customer\Model\ResourceModel\Queue\Collection'
        )->setPageSize(
            $countOfQueue
        )->setCurPage(
            1
        )->addOnlyForSendingFilter()->load();

        $collection->walk('sendPerDesign', [$countOfSubscritions]);
    }
}
