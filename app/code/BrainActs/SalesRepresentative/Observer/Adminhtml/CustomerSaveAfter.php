<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Observer\Adminhtml;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class CustomerSaveAfter
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class CustomerSaveAfter implements ObserverInterface
{

    /**
     * @var \BrainActs\SalesRepresentative\Model\ResourceModel\Member
     */
    private $memberResourceFactory;

    public function __construct(
        \BrainActs\SalesRepresentative\Model\ResourceModel\MemberFactory $memberResourceFactory
    ) {
        $this->memberResourceFactory = $memberResourceFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $request = $observer->getData('request');
        $customer = $observer->getData('customer');
        $customerId = $customer->getId();

        $members = $request->getParam('customer_members');

        if ($members == null) {
            return;
        }

        try {
            $data = json_decode($members, true);
        } catch (\Exception $e) {
            $data = null;
        }

        if ($data === null) {
            return;
        }

        $resource = $this->memberResourceFactory->create();
        $resource->removeMembersFromCustomer($customerId);


        foreach ($data as $id => $value) {
            $resource->applyMemberToCustomer($customerId, $id);
        }
    }
}
