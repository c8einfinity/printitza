<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Model;

use BrainActs\SalesRepresentative\Model\Links;
use BrainActs\SalesRepresentative\Model\ResourceModel\Report\CustomersFactory;
use BrainActs\SalesRepresentative\Model\ResourceModel\Report\OrdersFactory;
use BrainActs\SalesRepresentative\Model\ResourceModel\Report\ProductsFactory;
use BrainActs\SalesRepresentative\Model\ResourceModel\Report\ProfitFactory;
use BrainActs\SalesRepresentative\Helper\Email;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Class Convert
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class Convert
{

    const CUSTOMER_TYPE = 'customer';

    const PRODUCT_TYPE = 'product';

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var MemberFactory
     */
    private $memberFactory;

    private $orderItems;

    private $productsIds;

    private $customerId;

    private $members = [];

    /**
     * @var ProductsFactory
     */
    private $productsReportResource;

    /**
     * @var CustomersFactory
     */
    private $customersReportResource;

    /**
     * @var LinksFactory
     */
    private $linksFactory;

    /**
     * @var \Magento\Sales\Model\Order $order
     */
    private $order;

    /**
     * @var ResourceModel\Report\OrdersFactory
     */
    private $ordersFactory;

    /**
     * @var ProfitFactory
     */
    private $profitReportResource;

    /**
     * @var Email
     */
    private $emailHelper;

    /**
     * @var ResourceModel\Member
     */
    private $memberResource;

    /**
     * Convert constructor.
     * @param OrderRepositoryInterface $orderRepository
     * @param MemberFactory $memberFactory
     * @param ProductsFactory $productsReportResource
     * @param CustomersFactory $customersReportResource
     * @param ProfitFactory $profitReportResource
     * @param LinksFactory $linksFactory
     * @param OrdersFactory $ordersFactory
     * @param Email $emailHelper
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        MemberFactory $memberFactory,
        \BrainActs\SalesRepresentative\Model\ResourceModel\Member $memberResource,
        ProductsFactory $productsReportResource,
        CustomersFactory $customersReportResource,
        ProfitFactory $profitReportResource,
        LinksFactory $linksFactory,
        OrdersFactory $ordersFactory,
        Email $emailHelper
    ) {
        $this->orderRepository = $orderRepository;
        $this->memberFactory = $memberFactory;
        $this->productsReportResource = $productsReportResource;
        $this->customersReportResource = $customersReportResource;
        $this->linksFactory = $linksFactory;
        $this->ordersFactory = $ordersFactory;
        $this->profitReportResource = $profitReportResource;
        $this->emailHelper = $emailHelper;
        $this->memberResource = $memberResource;
    }

    /**
     * Start assign order/products/order to SR member
     * @param int $orderId
     * @throws \Exception
     */
    public function apply($orderId)
    {
        $this->order = $this->orderRepository->get($orderId);
        $this->customerId = $this->order->getCustomerId();
        $this->orderItems = $this->order->getItemsCollection();

        foreach ($this->orderItems as $orderItem) {
            $this->productsIds[] = $orderItem->getProductId();
        }

        $this->getSalesRepByCustomer($this->customerId);

        foreach ($this->productsIds as $id) {
            $this->getSalesRepByProduct($id);
        }

        $this->_apply();
    }

    /**
     * @param int $orderId
     * @param int $userId
     * @throws \Exception
     */
    public function autoAssignAdmin($orderId, $userId)
    {
        $this->order = $this->orderRepository->get($orderId);

        /** @var \BrainActs\SalesRepresentative\Model\Member $member */
        $member = $this->memberFactory->create();
        $member->load($userId, 'user_id');

        if ($member->getUserId() == $userId && $member->isActive()) {
            $this->linkOrder(
                $member->getId(),
                $this->order->getId(),
                Links::RULE_TYPE_ORDER_AUTOASSIGN
            );

            /** @var  \BrainActs\SalesRepresentative\Model\ResourceModel\Report\Orders $ordersReport */
            $ordersReport = $this->ordersFactory->create();

            $ordersReport->aggregate($orderId, $member->getId());

            /** @var \BrainActs\SalesRepresentative\Model\ResourceModel\Report\Profit $profitReport */
            $profitReport = $this->profitReportResource->create();

            $profitReport->aggregateByOrder($orderId, $member->getId());
        }
    }

    /**
     * @throws \Exception
     */
    private function _apply()
    {
        foreach ($this->members as $memberId => $data) {
            if (count($data['types']) == 1) { //if only one type
                switch ($data['types'][0]) {
                    case self::CUSTOMER_TYPE:
                        $this->applyOrderItem($memberId, $data);
                        $this->linkOrder(
                            $memberId,
                            $this->order->getId(),
                            Links::RULE_TYPE_CUSTOMER
                        );
                        break;
                    case self::PRODUCT_TYPE:
                        $this->applySimpleOrderItem($memberId, $data);
                        $this->linkOrder(
                            $memberId,
                            $this->order->getId(),
                            Links::RULE_TYPE_PRODUCT
                        );
                        break;
                }
            } elseif (count($data['types']) > 1) {//if more then one type
                $priorityType = $this->calcPriorityInMember($data);

                switch ($priorityType) {
                    case self::CUSTOMER_TYPE:
                        $this->applyOrderItem($memberId, $data);
                        $this->linkOrder(
                            $memberId,
                            $this->order->getId(),
                            Links::RULE_TYPE_CUSTOMER
                        );
                        break;
                    case self::PRODUCT_TYPE:
                        $this->applySimpleOrderItem($memberId, $data);
                        $this->linkOrder(
                            $memberId,
                            $this->order->getId(),
                            Links::RULE_TYPE_PRODUCT
                        );
                        break;
                }
            }
        }
    }

    private function calcPriorityInMember($member)
    {
        $productPriority = (int)$member['data']['product_priority'];
        $customerPriority = (int)$member['data']['customer_priority'];

        if ($productPriority < $customerPriority) {
            return self::PRODUCT_TYPE;
        }

        if ($productPriority > $customerPriority) {
            return self::CUSTOMER_TYPE;
        }

        //return as default - //TODO Move this to system/configuration/settings
        return self::PRODUCT_TYPE;
    }

    /**
     * Copy data from order to aggregate table
     * @param int $memberId
     * @param array $member
     * @throws \Exception
     */
    private function applyOrderItem($memberId, $member)
    {
        //save all items

        $params = [
            'member_id' => $memberId,
            'type' => $member['data']['customer_rate_type'],
            'value' => $member['data']['customer_value'],
            'increment_order_id' => $this->order->getIncrementId(),

        ];

        /** @var  \BrainActs\SalesRepresentative\Model\ResourceModel\Report\Customers $customerReport */
        $customerReport = $this->customersReportResource->create();
        $customerReport->aggregate($this->order->getId(), $params);

        /** @var \BrainActs\SalesRepresentative\Model\ResourceModel\Report\Profit $profitReport */
        $profitReport = $this->profitReportResource->create();
        $profitReport->aggregateByCustomer($this->order->getId(), $params);
    }

    /**
     * Copy data from simple order item to aggregate table
     * @param int $memberId
     * @param array $member
     * @throws \Exception
     */
    private function applySimpleOrderItem($memberId, $member)
    {

        $params = [
            'member_id' => $memberId,
            'type' => $member['data']['product_rate_type'],
            'value' => $member['data']['product_value'],
            'increment_order_id' => $this->order->getIncrementId(),

        ];

        /** @var  \BrainActs\SalesRepresentative\Model\ResourceModel\Report\Products $productReport */
        $productReport = $this->productsReportResource->create();

        /** @var \BrainActs\SalesRepresentative\Model\ResourceModel\Report\Profit $profitReport */
        $profitReport = $this->profitReportResource->create();

        foreach ($member['products'] as $productId) {
            foreach ($this->orderItems as $orderItem) {
                if ($orderItem->getProductId() == $productId) {
                    $productReport->aggregate($orderItem->getId(), $this->order->getId(), $params);

                    $profitReport->aggregateByProduct($orderItem->getId(), $this->order->getId(), $params);
                }
            }
        }
    }

    /**
     * Get Member ids by customer id
     *
     * @param $customerId
     * @return $this
     */
    private function getSalesRepByCustomer($customerId)
    {

        /** @var  \BrainActs\SalesRepresentative\Model\ResourceModel\Member\Collection $collection */
        $collection = $this->memberFactory->create()->getCollection();

        $collection->addCustomerFilter($customerId)->load();

        foreach ($collection as $member) {
            $this->members[$member->getId()] = [
                'types' => [self::CUSTOMER_TYPE],
                'data' => $member->getData()
            ];
        }

        return $this;
    }

    /**
     * Get Member ids by product ids
     *
     * @param $productId
     * @return $this
     */
    private function getSalesRepByProduct($productId)
    {
        $collection = $this->memberFactory->create()->getCollection()
            ->addProductFilter($productId)->load();

        foreach ($collection as $member) {
            $this->members[$member->getId()]['types'][] = self::PRODUCT_TYPE;
            $this->members[$member->getId()]['data'] = $member->getData();

            if (!isset($this->members[$member->getId()]['products'])) {
                $this->members[$member->getId()]['products'] = [$productId];
            } else {
                $this->members[$member->getId()]['products'][] = $productId;
            }
        }

        return $this;
    }

    /**
     * @param $memberId
     * @param $orderId
     * @param $ruleType
     * @return bool
     */
    public function linkOrder($memberId, $orderId, $ruleType)
    {
        $model = $this->linksFactory->create();
        $data = [
            'member_id' => $memberId,
            'order_id' => $orderId,
            'rule' => $ruleType,
        ];

        $model->setData($data);

        try {
            $model->save();
            $this->emailHelper->notifySalesRepresentative($memberId, $orderId);
        } catch (LocalizedException $e) {
            return false;
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * @param int $memberId
     * @param int $customerId
     * @param OrderInterface $order
     * @throws LocalizedException
     * @throws \Exception
     */
    public function applyCustomerToMember($memberId, $customerId, OrderInterface $order)
    {
        $this->order = $order;

        //Check if customer is assigned to member
        $member = $this->memberFactory->create();
        $member->load($memberId);

        $customersIds = $this->memberResource->lookupCustomerIds($member->getId());

        if (!in_array($customerId, $customersIds)) {
            //remove customer from other members
            $this->memberResource->removeMembersFromCustomer($customerId);

            //assign to member
            $this->memberResource->applyMemberToCustomer($customerId, $member->getId());

            //assign by customer.
            $memberFormat = [
                'data' => $member->getData()
            ];

            $this->applyOrderItem($member->getId(), $memberFormat);

            $this->linkOrder(
                $member->getId(),
                $order->getId(),
                Links::RULE_TYPE_CUSTOMER
            );
        }
    }
}
