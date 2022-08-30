<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Controller\Adminhtml\Order;

use BrainActs\SalesRepresentative\Model\ResourceModel\Report\OrdersFactory;
use BrainActs\SalesRepresentative\Model\ResourceModel\Report\ProfitFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;

/**
 * Class Assign
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class Assign extends \Magento\Backend\App\Action
{
    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var OrdersFactory
     */
    private $ordersReportResource;

    /**
     * @var ProfitFactory
     */
    private $profitReportResource;

    /**
     * @var BrainActs\SalesRepresentative\Model\LinksFactory
     */
    private $linksFactory;

    /**
     * @var \BrainActs\SalesRepresentative\Helper\Email
     */
    private $emailHelper;

    /**
     * Assign constructor.
     * @param Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param OrdersFactory $ordersReportResource
     * @param ProfitFactory $profitReportResource
     */
    public function __construct(
        Context $context,
        DataPersistorInterface $dataPersistor,
        OrdersFactory $ordersReportResource,
        ProfitFactory $profitReportResource,
        \BrainActs\SalesRepresentative\Model\LinksFactory $linksFactory,
        \BrainActs\SalesRepresentative\Helper\Email $emailHelper
    ) {

        $this->dataPersistor = $dataPersistor;
        parent::__construct($context);
        $this->ordersReportResource = $ordersReportResource;
        $this->profitReportResource = $profitReportResource;
        $this->linksFactory = $linksFactory;
        $this->emailHelper = $emailHelper;
    }

    /**
     * Save action
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     * @throws \Exception
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();

        if ($data) {
            $id = $this->getRequest()->getParam('id');
            $orderId = $this->getRequest()->getParam('order_id');

            /** @var \BrainActs\SalesRepresentative\Model\Links $model */
            $model = $this->linksFactory->create();

            if (isset($data['checked']) && $data['checked'] === 'true') {
                //add
                $model->setData([
                    'member_id' => $id,
                    'order_id' => $orderId,
                    'rule' => \BrainActs\SalesRepresentative\Model\Links::RULE_TYPE_ORDER,
                ])->save();

                $this->emailHelper->notifySalesRepresentative($id, $orderId);
                /** @var  \BrainActs\SalesRepresentative\Model\ResourceModel\Report\Orders $ordersReport */
                $ordersReport = $this->ordersReportResource->create();

                $ordersReport->aggregate($orderId, $id);

                /** @var \BrainActs\SalesRepresentative\Model\ResourceModel\Report\Profit $profitReport */
                $profitReport = $this->profitReportResource->create();

                $profitReport->aggregateByOrder($orderId, $id, true);
            } else {
                //remove
                $collection = $model->getCollection()->addFieldToFilter('order_id', $orderId)
                    ->addFieldToFilter('member_id', $id);

                /** @var \BrainActs\SalesRepresentative\Model\Links $item */
                $item = $collection->getFirstItem();

                $this->removeFromAggregate($item);

                $item->delete();
            }
        }
    }

    private function removeFromAggregate($link)
    {
        $resourceModel = false;
        switch ($link->getRule()) {
            case \BrainActs\SalesRepresentative\Model\Links::RULE_TYPE_CUSTOMER:
                $resourceModel = $this->_objectManager
                    ->create(\BrainActs\SalesRepresentative\Model\ResourceModel\Report\Customers::class);
                $table = 'brainacts_salesrep_report_customer';
                break;
            case \BrainActs\SalesRepresentative\Model\Links::RULE_TYPE_PRODUCT:
                $resourceModel = $this->_objectManager
                    ->create(\BrainActs\SalesRepresentative\Model\ResourceModel\Report\Products::class);
                $table = 'brainacts_salesrep_report_product';
                break;
            case \BrainActs\SalesRepresentative\Model\Links::RULE_TYPE_REGION:
                break;
            case \BrainActs\SalesRepresentative\Model\Links::RULE_TYPE_ORDER:
                $resourceModel = $this->_objectManager
                    ->create(\BrainActs\SalesRepresentative\Model\ResourceModel\Report\Orders::class);
                $table = 'brainacts_salesrep_report_order';
                break;
        }

        if ($resourceModel) {
            $resourceModel->clear($link->getOrderId(), $link->getMemberId(), $table);
            $resourceModel = $this->_objectManager
                ->create(\BrainActs\SalesRepresentative\Model\ResourceModel\Report\Profit::class);
            $resourceModel->clear($link->getOrderId(), $link->getMemberId(), 'brainacts_salesrep_report_profit');
        }
    }
}
