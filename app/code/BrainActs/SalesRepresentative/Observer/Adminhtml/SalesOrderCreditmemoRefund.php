<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Observer\Adminhtml;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class SalesOrderCreditmemoRefund
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class SalesOrderCreditmemoRefund implements ObserverInterface
{
    /**
     * @var \BrainActs\SalesRepresentative\Model\LinksFactory
     */
    private $linksFactory;

    /**
     * @var \BrainActs\SalesRepresentative\Model\ResourceModel\Report\Customers
     */
    private $customersReportResource;

    /**
     * @var \BrainActs\SalesRepresentative\Model\ResourceModel\Report\Products
     */
    private $productsReportResource;

    /**
     * @var \BrainActs\SalesRepresentative\Model\ResourceModel\Report\Orders
     */
    private $ordersReportResource;

    /**
     * @var \BrainActs\SalesRepresentative\Model\ResourceModel\Report\Profit
     */
    private $profitReportResource;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * OrderCancelAfter constructor.
     * @param \BrainActs\SalesRepresentative\Model\LinksFactory $linksFactory
     */
    public function __construct(
        \BrainActs\SalesRepresentative\Model\LinksFactory $linksFactory,
        \BrainActs\SalesRepresentative\Model\ResourceModel\Report\Customers $customers,
        \BrainActs\SalesRepresentative\Model\ResourceModel\Report\Products $products,
        \BrainActs\SalesRepresentative\Model\ResourceModel\Report\Orders $orders,
        \BrainActs\SalesRepresentative\Model\ResourceModel\Report\Profit $profit,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
    ) {
        $this->linksFactory = $linksFactory;
        $this->customersReportResource = $customers;
        $this->productsReportResource = $products;
        $this->ordersReportResource = $orders;
        $this->profitReportResource = $profit;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param Observer $observer
     * @return void
     * @throws \Exception
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Sales\Api\Data\CreditmemoInterface $creditmemo */
        $creditmemo = $observer->getEvent()->getData('creditmemo');

//        $grandTotal = $creditmemo->getGrandTotal();
//
//        $order = $this->orderRepository->get($creditmemo->getOrderId());
//        $order->getGrandTotal();

        /** @var \BrainActs\SalesRepresentative\Model\Links $model */
        $model = $this->linksFactory->create();

        //remove all members from order
        $collection = $model->getCollection()->addFieldToFilter('order_id', $creditmemo->getOrderId());

        /** @var \BrainActs\SalesRepresentative\Model\Links $item */
        foreach ($collection as $item) {
            $this->removeFromAggregate($item);
            $item->delete();//@codingStandardsIgnoreLine
        }
    }

    /**
     * @param $link
     * @throws \Exception
     */
    private function removeFromAggregate(\BrainActs\SalesRepresentative\Model\Links $link)
    {
        $resourceModel = false;
        $table = false;
        switch ($link->getRule()) {
            case \BrainActs\SalesRepresentative\Model\Links::RULE_TYPE_CUSTOMER:
                $resourceModel = $this->customersReportResource;
                $table = 'brainacts_salesrep_report_customer';
                break;
            case \BrainActs\SalesRepresentative\Model\Links::RULE_TYPE_PRODUCT:
                $resourceModel = $this->productsReportResource;
                $table = 'brainacts_salesrep_report_product';
                break;
            case \BrainActs\SalesRepresentative\Model\Links::RULE_TYPE_REGION:
                break;
            case \BrainActs\SalesRepresentative\Model\Links::RULE_TYPE_ORDER:
                $resourceModel = $this->ordersReportResource;
                $table = 'brainacts_salesrep_report_order';
                break;
        }

        if ($resourceModel && $table) {
            $resourceModel->clear($link->getOrderId(), $link->getMemberId(), $table);
            $resourceModel = $this->profitReportResource;
            $resourceModel->clear($link->getOrderId(), $link->getMemberId(), 'brainacts_salesrep_report_profit');
        }
    }
}
