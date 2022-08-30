<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class InvoiceEmail
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class InvoiceEmail implements ObserverInterface
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
        $transport = $observer->getData('transportObject');

        if (is_object($transport) && $transport != null) {
            $orderId = $transport->getOrder()->getId();
            $customerId = $transport->getOrder()->getCustomerId();
        } else {
            return;
        }

        $names = $this->getSalesRepresentative($customerId);
        $transport->setSalesRepresentative($names);
    }

    /**
     * @param $customerId
     * @return string
     */
    private function getSalesRepresentative($customerId)
    {
        $collection = $this->memberCollectionFactory->create();
        $customerTable = $collection->getTable('brainacts_salesrep_member_customer');

        $collection->join(['related' => $customerTable], 'main_table.member_id = related.member_id', [])
            ->addFieldToFilter('related.customer_id', ['eq' => $customerId]);

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
