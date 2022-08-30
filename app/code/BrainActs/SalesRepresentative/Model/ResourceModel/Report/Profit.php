<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Model\ResourceModel\Report;

use BrainActs\SalesRepresentative\Model\Links;
use Magento\Store\Model\Store;

/**
 * Class Profit
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
//@codingStandardsIgnoreFile
class Profit extends AbstractReport
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product
     */
    public $_productResource;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Helper
     */
    public $salesResourceHelper;

    /**
     * @var \BrainActs\SalesRepresentative\Model\MemberFactory
     */
    private $memberFactory;

    /**
     * Ignored product types list
     *
     * @var array
     */
    protected $ignoredProductTypes = [
        \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE => \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE,
    ];

    /**
     * Profit constructor.
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Reports\Model\FlagFactory $reportsFlagFactory
     * @param \Magento\Framework\Stdlib\DateTime\Timezone\Validator $timezoneValidator
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Magento\Catalog\Model\ResourceModel\Product $productResource
     * @param \Magento\Sales\Model\ResourceModel\Helper $salesResourceHelper
     * @param \BrainActs\SalesRepresentative\Model\MemberFactory $memberFactory
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \BrainActs\SalesRepresentative\Model\ConfigFactory $configFactory
     * @param null $connectionName
     * @param array $ignoredProductTypes
     */
    public function __construct(//@codingStandardsIgnoreLine
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Reports\Model\FlagFactory $reportsFlagFactory,
        \Magento\Framework\Stdlib\DateTime\Timezone\Validator $timezoneValidator,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Catalog\Model\ResourceModel\Product $productResource,
        \Magento\Sales\Model\ResourceModel\Helper $salesResourceHelper,
        \BrainActs\SalesRepresentative\Model\MemberFactory $memberFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \BrainActs\SalesRepresentative\Model\ConfigFactory $configFactory,
        $connectionName = null,
        array $ignoredProductTypes = []
    )
    {
        parent::__construct(
            $context,
            $logger,
            $localeDate,
            $reportsFlagFactory,
            $timezoneValidator,
            $dateTime,
            $connectionName
        );
        $this->_productResource = $productResource;
        $this->salesResourceHelper = $salesResourceHelper;
        $this->memberFactory = $memberFactory;
        $this->ignoredProductTypes = array_merge($this->ignoredProductTypes, $ignoredProductTypes);

        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository;
        $this->configFactory = $configFactory;
    }

    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()//@codingStandardsIgnoreLine
    {
        $this->_init('brainacts_salesrep_report_profit', 'id');
    }

    /**
     * @param $orderItemId
     * @param $orderId
     * @param $data
     * @return $this
     * @throws \Exception
     */
    public function aggregateByProduct($orderItemId, $orderId, $data)
    {

        $connection = $this->getConnection();
        $costEarn = $this->getEarnBasedOnCostByOrderItem($orderId, $orderItemId);
        try {
            $subSelect = null;

            $select = $connection->select();

            $select->group(['source_table.store_id', 'order_item.product_id']);

            $columns = [
                'period' => 'source_table.created_at',
                'store_id' => 'source_table.store_id',
                'product_id' => 'order_item.product_id',
                'product_name' => new \Zend_Db_Expr('MIN(order_item.name)'),
                'product_price' => new \Zend_Db_Expr(
                    'MIN(IF(order_item_parent.base_price, order_item_parent.base_price, order_item.base_price))* MIN(source_table.base_to_global_rate)'
                ),
                'qty_ordered' => new \Zend_Db_Expr('SUM(order_item.qty_ordered)'),
                'member_id' => new \Zend_Db_Expr($data['member_id']),
                'order_id' => new \Zend_Db_Expr($orderId),
                'rate_type' => new \Zend_Db_Expr($data['type']),
                'rate_value' => new \Zend_Db_Expr($data['value']),
                'increment_order_id' => new \Zend_Db_Expr("'{$data['increment_order_id']}'"),
                'order_item_id' => 'order_item.item_id',
                'rule_type' => new \Zend_Db_Expr(Links::RULE_TYPE_PRODUCT),

            ];

            if ($data['type'] == 1) {
                //Percent
                $exp1 = '(MIN(IF(order_item_parent.base_price, order_item_parent.base_price, order_item.base_price))*MIN(source_table.base_to_global_rate))';
                $exp2 = '(MIN(IF(order_item_parent.base_discount_amount, order_item_parent.base_discount_amount, order_item.base_discount_amount))/order_item.qty_ordered)';

                if ($costEarn === false) {
                    $columns['earn'] = new \Zend_Db_Expr(
                        "({$exp1}-{$exp2})" .
                        ' / 100 * ' . $data['value'] . ' * order_item.qty_ordered'
                    );
                } else {
                    $columns['earn'] = new \Zend_Db_Expr(
                        '(' . $costEarn . " / 100 * " . $data['value'] . ')'
                    );
                }
            }

            if ($data['type'] == 2) {
                //Fixed
                $columns['earn'] = new \Zend_Db_Expr($data['value']);
            }

            $select->from(
                ['source_table' => $this->getTable('sales_order')],
                $columns
            )->joinInner(
                ['order_item' => $this->getTable('sales_order_item')],
                'order_item.order_id = source_table.entity_id',
                []
            )->joinLeft(
                ['order_item_parent' => $this->getTable('sales_order_item')],
                'order_item.parent_item_id = order_item_parent.item_id',
                []
            )->where(
                'source_table.state != ?',
                \Magento\Sales\Model\Order::STATE_CANCELED
            )->where(
                'order_item.product_type NOT IN(?)',
                $this->ignoredProductTypes
            )->where(
                'order_item.item_id= ?',
                $orderItemId
            )->where(
                'source_table.entity_id= ?',
                $orderId
            );

            $select->useStraightJoin();

            $insertQuery = $select->insertFromSelect($this->getMainTable(), array_keys($columns));
            $connection->query($insertQuery);
        } catch (\Exception $e) {
            throw $e;
        }

        return $this;
    }

    /**
     * @param $orderId
     * @param $data
     * @return $this
     * @throws \Exception
     */
    public function aggregateByCustomer($orderId, $data)
    {

        $connection = $this->getConnection();
        $costEarn = $this->getEarnBasedOnCost($orderId);
        try {
            $subSelect = null;

            $select = $connection->select();

            $select->group(['source_table.store_id']);

            $columns = [
                'period' => 'source_table.created_at',
                'store_id' => 'source_table.store_id',
                'order_price' => new \Zend_Db_Expr(
                    'IF(source_table.base_subtotal, source_table.base_subtotal, source_table.base_subtotal) *' .
                    ' source_table.base_to_global_rate'
                ),

                'member_id' => new \Zend_Db_Expr($data['member_id']),
                'order_id' => new \Zend_Db_Expr($orderId),
                'rate_type' => new \Zend_Db_Expr($data['type']),
                'rate_value' => new \Zend_Db_Expr($data['value']),
                'rule_type' => new \Zend_Db_Expr(Links::RULE_TYPE_CUSTOMER),
                'increment_order_id' => new \Zend_Db_Expr("'{$data['increment_order_id']}'")
            ];

            if ($data['type'] == 1) {
                //Percent
                if ($costEarn === false) {
                    $columns['earn'] = new \Zend_Db_Expr(
                        '((source_table.base_subtotal * source_table.base_to_global_rate) - ABS(source_table.base_discount_amount)) / 100 * ' . $data['value']
                    );
                } else {
                    $columns['earn'] = new \Zend_Db_Expr(
                        $costEarn . ' / 100 * ' . $data['value']
                    );
                }
            }
            if ($data['type'] == 2) {
                //Fixed
                $columns['earn'] = new \Zend_Db_Expr($data['value']);
            }

            $select->from(
                ['source_table' => $this->getTable('sales_order')],
                $columns
            )->where(
                'source_table.state != ?',
                \Magento\Sales\Model\Order::STATE_CANCELED
            )->where(
                'source_table.entity_id= ?',
                $orderId
            );

            $select->useStraightJoin();

            $insertQuery = $select->insertFromSelect($this->getMainTable(), array_keys($columns));
            $connection->query($insertQuery);
        } catch (\Exception $e) {
            throw $e;
        }

        return $this;
    }

    /**
     * Aggregate Orders data by order created at
     *
     *
     * @param $orderId
     * @param $memberId
     * @param bool $manually
     * @return $this
     * @throws \Exception
     * @internal param $type
     */
    public function aggregateByOrder($orderId, $memberId, $manually = false)
    {

        if (!$manually) {
            $type = Links::RULE_TYPE_ORDER_AUTOASSIGN;
        } else {
            $type = Links::RULE_TYPE_ORDER;
        }
        /** @var \BrainActs\SalesRepresentative\Model\Member $member */
        $member = $this->getMember($memberId);
        $costEarn = $this->getEarnBasedOnCost($orderId);

        $connection = $this->getConnection();

        try {
            $subSelect = null;

            $select = $connection->select();

            $select->group(['source_table.store_id']);

            $columns = [
                'period' => 'source_table.created_at',
                'store_id' => 'source_table.store_id',
                'increment_order_id' => 'source_table.increment_id',
                'order_price' => new \Zend_Db_Expr(
                    'IF(source_table.base_subtotal, source_table.base_subtotal, source_table.base_subtotal) *' .
                    ' source_table.base_to_global_rate'
                ),

                'member_id' => new \Zend_Db_Expr($member->getId()),
                'order_id' => new \Zend_Db_Expr($orderId),
                'rate_type' => new \Zend_Db_Expr($member->getOrderRateType()),
                'rate_value' => new \Zend_Db_Expr($member->getOrderValue()),
                'rule_type' => new \Zend_Db_Expr($type),
            ];

            if ($member->getOrderRateType() == 1) {
                //Percent
                if ($costEarn === false) {
                    $columns['earn'] = new \Zend_Db_Expr(
                        '((source_table.base_subtotal * source_table.base_to_global_rate) - ABS(source_table.base_discount_amount)) / 100 * ' . $member->getOrderValue()
                    );
                } else {
                    $columns['earn'] = new \Zend_Db_Expr(
                        $costEarn . ' / 100 * ' . $member->getOrderValue()
                    );
                }
            }
            if ($member->getOrderRateType() == 2) {
                //Fixed
                $columns['earn'] = new \Zend_Db_Expr($member->getOrderValue());
            }

            $select->from(
                ['source_table' => $this->getTable('sales_order')],
                $columns
            )->where(
                'source_table.state != ?',
                \Magento\Sales\Model\Order::STATE_CANCELED
            )->where(
                'source_table.entity_id= ?',
                $orderId
            );

            $select->useStraightJoin();

            $insertQuery = $select->insertFromSelect($this->getMainTable(), array_keys($columns));
            $connection->query($insertQuery);
        } catch (\Exception $e) {
            throw $e;
        }

        return $this;
    }

    /**
     * @param $memberId
     * @return \BrainActs\SalesRepresentative\Model\Member
     */
    private function getMember($memberId)
    {
        /** @var \BrainActs\SalesRepresentative\Model\Member $model */
        $model = $this->memberFactory->create();
        $member = $model->load($memberId);
        return $member;
    }

    /**
     * Get Lifetime Commissions
     * @param int $memberId
     * @param bool $limitDate
     * @return string
     */
    public function getLifeTimeCommissions($memberId, $limitDate = false, $cond = '<')
    {
        $connection = $this->getConnection();

        $select = $connection->select();
        $columns = [
            'aggregate' => new \Zend_Db_Expr('SUM(source_table.earn)')
        ];

        $select->from(
            ['source_table' => $this->getTable('brainacts_salesrep_report_profit')],
            $columns
        )->where(
            'source_table.member_id= ?',
            $memberId
        );

        if ($limitDate) {
            $select->where(
                'source_table.period ' . $cond . ' ?',
                $limitDate
            );
        }

        $result = $connection->fetchOne($select);
        return $result;
    }

    /**
     * Get Pending amount
     * @param int $memberId
     * @param int $period
     * @return string
     */
    public function getPendingCommission($memberId, $period)
    {
        $connection = $this->getConnection();

        $select = $connection->select();
        $columns = [
            'aggregate' => new \Zend_Db_Expr('SUM(source_table.earn)')
        ];

        $select->from(
            ['source_table' => $this->getTable('brainacts_salesrep_report_profit')],
            $columns
        )->where(
            'source_table.member_id= ?',
            $memberId
        )->where(
            'source_table.period > ?',
            new \Zend_Db_Expr('CURDATE() - INTERVAL ' . $period . ' DAY')
        );

        $result = $connection->fetchOne($select);
        return $result;
    }

    /**
     * Get payout amount by user
     * @param $memberId
     * @param bool $limitDate
     * @return string
     */
    public function getPayout($memberId, $limitDate = false)
    {
        $connection = $this->getConnection();

        $select = $connection->select();
        $columns = [
            'aggregate' => new \Zend_Db_Expr('SUM(source_table.amount)')
        ];

        $select->from(
            ['source_table' => $this->getTable('brainacts_salesrep_withdrawals')],
            $columns
        )->where(
            'source_table.member_id= ?',
            $memberId
        )->where(
            'source_table.status != ?',
            \BrainActs\SalesRepresentative\Model\Withdrawals::STATUS_CANCELED
        );

        if ($limitDate) {
            $select->where(
                'source_table.creation_time < ?',
                $limitDate
            );
        }

        $result = $connection->fetchOne($select);
        return $result;
    }

    /**
     * Get payout amount by user
     * @param $memberId
     * @param bool $limitDate
     * @param string $cond
     * @return string
     */
    public function getPayoutCompleted($memberId, $limitDate = false, $cond = '<')
    {
        $connection = $this->getConnection();

        $select = $connection->select();
        $columns = [
            'aggregate' => new \Zend_Db_Expr('SUM(source_table.amount)')
        ];

        $select->from(
            ['source_table' => $this->getTable('brainacts_salesrep_withdrawals')],
            $columns
        )->where(
            'source_table.member_id= ?',
            $memberId
        )->where(
            'source_table.status = ?',
            \BrainActs\SalesRepresentative\Model\Withdrawals::STATUS_COMPLETED
        );

        if ($limitDate) {
            $select->where(
                'source_table.creation_time ' . $cond . ' ?',
                new \Zend_Db_Expr("'{$limitDate}' + INTERVAL 1 DAY")
            );
        }

        $result = $connection->fetchOne($select);
        return $result;
    }


    /**
     * @param int $withdrawalId
     * @param float $amount
     * @return $this
     * @throws \Exception
     */
    public function insertWithdrawal($withdrawalId, $amount, $memberId)
    {

        $connection = $this->getConnection();

        $table = $this->getTable('brainacts_salesrep_report_profit');

        try {
            $date = date('Y-m-d');
            $sql = "INSERT INTO " . $table . " (withdrawal, withdrawal_id, member_id, period) VALUES ({$amount},{$withdrawalId}, {$memberId}, '{$date}')";
            $connection->query($sql);
        } catch (\Exception $e) {
            throw $e;
        }

        return $this;
    }

    public function removeWithdrawal($withdrawalId)
    {

        $connection = $this->getConnection();

        $table = $this->getTable('brainacts_salesrep_report_profit');

        try {
            $sql = "DELETE FROM " . $table . " WHERE withdrawal_id={$withdrawalId}";
            $connection->query($sql);
        } catch (\Exception $e) {
            throw $e;
        }

        return $this;
    }

}
