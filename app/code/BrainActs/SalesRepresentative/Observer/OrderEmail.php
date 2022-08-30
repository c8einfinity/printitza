<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class OrderEmail
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class OrderEmail implements ObserverInterface
{

    /**
     * @var \BrainActs\SalesRepresentative\Model\ResourceModel\Member\CollectionFactory
     */
    private $memberCollectionFactory;

    /**
     * @var \BrainActs\SalesRepresentative\Model\ResourceModel\MemberFactory
     */
    private $memberResourceFactory;

    /**
     * OrderEmail constructor.
     * @param \BrainActs\SalesRepresentative\Model\ResourceModel\Member\CollectionFactory $memberCollectionFactory
     * @param \BrainActs\SalesRepresentative\Model\ResourceModel\MemberFactory $memberResourceFactory
     */
    public function __construct(
        \BrainActs\SalesRepresentative\Model\ResourceModel\Member\CollectionFactory $memberCollectionFactory,
        \BrainActs\SalesRepresentative\Model\ResourceModel\MemberFactory $memberResourceFactory
    ) {
        $this->memberCollectionFactory = $memberCollectionFactory;
        $this->memberResourceFactory = $memberResourceFactory;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Framework\DataObject|[] $transport */
        $transport = $observer->getTransport();
        if (is_object($transport)) {
            $customerId = $transport->getOrder()->getCustomerId();
        } else {
            $customerId = $transport['order']->getCustomerId();
        }

        $names = $this->getSalesRepresentative($customerId);

        if (is_object($transport)) {
            $transport->setSalesRepresentative($names);
        } else {
            $transport['sales_representative'] = $names;
        }
    }


    /**
     * @param $customerId
     * @return \Magento\Framework\Phrase|string
     */
    private function getSalesRepresentative($customerId)
    {
        $collection = $this->memberCollectionFactory->create();
        $customerTable = $collection->getTable('brainacts_salesrep_member_customer');

        $collection->join(
            ['related' => $customerTable],
            'main_table.member_id = related.member_id',
            []
        )->addFieldToFilter('related.customer_id', ['eq' => $customerId]);


        $size = $collection->getSize();
        if ($size) {
            $names = [];
            foreach ($collection as $member) {
                $names [] = implode(' ', [$member->getFirstname(), $member->getLastname()]);
            }

            return implode(', ', $names);
        }
        return __('N/A');
    }
}
