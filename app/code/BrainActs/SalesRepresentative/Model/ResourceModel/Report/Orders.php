<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Model\ResourceModel\Report;

/**
 * Class Orders
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class Orders extends AbstractReport
{

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product
     */
    public $productResource;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Helper
     */
    public $salesResourceHelper;

    /**
     * Ignored product types list
     *
     * @var array
     */
    public $ignoredProductTypes = [
        \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE => \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE,
    ];

    /**
     * @var \BrainActs\SalesRepresentative\Model\MemberFactory
     */
    public $memberFactory;

    /**
     * Orders constructor.
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
    ) {

        parent::__construct(
            $context,
            $logger,
            $localeDate,
            $reportsFlagFactory,
            $timezoneValidator,
            $dateTime,
            $connectionName
        );
        $this->productResource = $productResource;
        $this->salesResourceHelper = $salesResourceHelper;
        $this->ignoredProductTypes = array_merge($this->ignoredProductTypes, $ignoredProductTypes);
        $this->memberFactory = $memberFactory;
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
        $this->_init('brainacts_salesrep_report_order', 'id');
    }

    private function getMember($memberId)
    {
        /** @var \BrainActs\SalesRepresentative\Model\Member $model */
        $model = $this->memberFactory->create();
        $member = $model->load($memberId);
        return $member;
    }

    /**
     * Aggregate Orders data by order created at
     *
     *
     * @param $orderId
     * @param $memberId
     * @return $this
     * @throws \Exception
     * @internal param $type
     */
    //@codingStandardsIgnoreStart
    public function aggregate($orderId, $memberId)
    {

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
                'rate_value' => new \Zend_Db_Expr($member->getOrderValue())
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
    //@codingStandardsIgnoreEnd
}
