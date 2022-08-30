<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Model\ResourceModel\Report;

/**
 * Class AbstractReport
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
abstract class AbstractReport extends \Magento\Reports\Model\ResourceModel\Report\AbstractReport
{

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    public $orderRepository;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    public $productRepository;

    /**
     * @var \BrainActs\SalesRepresentative\Model\ConfigFactory
     */
    public $configFactory;

    /**
     * Clear Aggregated table
     *
     * @param $orderId
     * @param $memberId
     * @param $table
     * @throws \Exception
     */
    public function clear($orderId, $memberId, $table)
    {
        $connection = $this->getConnection();
        $select = $connection->select();

        $select->from(
            ['source_table' => $this->getTable($table)],
            []
        )->where(
            'source_table.order_id = ?',
            $orderId
        )->where(
            'source_table.member_id= ?',
            $memberId
        );

        try {
            $deleteQuery = $select->deleteFromSelect('source_table');
            $connection->query($deleteQuery);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @param int $orderId
     * @return float|int|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getEarnBasedOnCost($orderId)
    {
        $config = $this->configFactory->create();
        $attribute = $config->useCostInCalculation();
        if ($attribute == false) {
            return false;
        }
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->orderRepository->get($orderId);
        //$items = $order->getItems();
        $items = $order->getAllVisibleItems();
        $orderEarn = 0;
        foreach ($items as $item) {
            if ($item->getProductType() == \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE) {
                $orderEarn = $orderEarn + $this->getSimple($item, $attribute);
            }

            if ($item->getProductType() == \Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL) {
                $orderEarn = $orderEarn + $this->getSimple($item, $attribute);//TODO CHECK
            }

            if ($item->getProductType() == \Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE) {
                $orderEarn = $orderEarn + $this->getSimple($item, $attribute);//TODO CHECK
            }

            if ($item->getProductType() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
                $orderEarn = $orderEarn + $this->getSimple($item, $attribute);
            }

            if ($item->getProductType() == \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
                $orderEarn = $orderEarn + $this->getBundle($item, $order, $attribute);
            }
        }
        return $orderEarn;
    }

    /**
     * @param int $orderId
     * @param int $orderItemId
     * @return float|int|null
     */
    public function getEarnBasedOnCostByOrderItem($orderId, $orderItemId)
    {
        $config = $this->configFactory->create();
        $attribute = $config->useCostInCalculation();
        if ($attribute == false) {
            return false;
        }
        /** var \Magento\Sales\Model\Order $order */
        $order = $this->orderRepository->get($orderId);
        $items = $order->getItems();
        //$items = $order->getAllVisibleItems();
        $orderEarn = 0;

        foreach ($items as $item) {
            if ($orderItemId == $item->getItemId()) {
//                if ($item->getParentItemId()) {
//                    $parent = $this->getParentItem($items, $item->getParentItemId());
//
//                    if ($parent->getProductType()!=\Magento\Catalog\Model\Product\Type::TYPE_BUNDLE){
//                        $item ==$parent;
//                    }else{
//                        $item
//                    }
//                }

                if ($item->getProductType() == \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE) {
                    $orderEarn = $orderEarn + $this->getSimple($item, $attribute, $items);
                }

                if ($item->getProductType() == \Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL) {
                    $orderEarn = $orderEarn + $this->getSimple($item, $attribute, $items);
                }

                if ($item->getProductType() == \Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE) {
                    $orderEarn = $orderEarn + $this->getSimple($item, $attribute, $items);
                }

                if ($item->getProductType() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
                    $orderEarn = $orderEarn + $this->getSimple($item, $attribute, $items);
                }

                if ($item->getProductType() == \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
                    $orderEarn = $orderEarn + $this->getBundle($item, $order, $attribute);
                }
            }
        }
        return $orderEarn;
    }

    private function getParentItem($items, $id)
    {
        foreach ($items as $item) {
            if ($item->getItemId() == $id) {
                return $item;
            }
        }
    }

    private function getSimple($item, $attribute, $items = false)
    {
        $product = $this->productRepository->get($item->getSku());

        $cost = $product->getData($attribute);

        if (!$cost) {
            return 0;
        }

        $rowPrice = (float)$item->getBaseRowTotal();
        if (!$rowPrice && $item->getParentItemId()) {
            $parent = $this->getParentItem($items, $item->getParentItemId());
            $rowPrice = $parent->getBaseRowTotal();
        }
        $rowCost = $cost * $item->getQtyOrdered();
        $earn = $rowPrice - $rowCost;
        return $earn;
    }

    private function getBundle($bundleItem, $order, $attribute)
    {
        $orderEarn = 0;
        $items = $order->getItems();
        foreach ($items as $item) {
            if ($item->getParentItemId() == $bundleItem->getId()) {
                $orderEarn = $orderEarn + $this->getSimple($item, $attribute);
            }
        }

        return $orderEarn;
    }
}
