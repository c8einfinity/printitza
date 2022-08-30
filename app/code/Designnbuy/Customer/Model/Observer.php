<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Customer\Model;

use Magento\Cron\Model\Schedule;

/**
 * Customer module observer
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Observer
{
    /**
     * Queue collection factory
     *
     * @var \Designnbuy\Customer\Model\ResourceModel\Queue\CollectionFactory
     */
    protected $_queueCollectionFactory;

    /**
     * Construct
     *
     * @param \Designnbuy\Customer\Model\ResourceModel\Queue\CollectionFactory $queueCollectionFactory
     */
    public function __construct(
        \Designnbuy\Customer\Model\ResourceModel\Queue\CollectionFactory $queueCollectionFactory
    ) {
        $this->_queueCollectionFactory = $queueCollectionFactory;
    }

    /**
     * Scheduled send handler
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function scheduledSend()
    {
        $countOfQueue  = 3;
        $countOfSubscriptions = 20;

        /** @var \Designnbuy\Customer\Model\ResourceModel\Queue\Collection $collection */
        $collection = $this->_queueCollectionFactory->create();
        $collection->setPageSize($countOfQueue)->setCurPage(1)->addOnlyForSendingFilter()->load();

        $collection->walk('sendPerDesign', [$countOfSubscriptions]);
    }
}
