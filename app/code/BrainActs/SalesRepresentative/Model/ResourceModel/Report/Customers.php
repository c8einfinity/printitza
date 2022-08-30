<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Model\ResourceModel\Report;

/**
 * Class Customers
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
//@codingStandardsIgnoreFile
class Customers extends AbstractReport
{
    const AGGREGATION_DAILY = 'daily';

    const AGGREGATION_MONTHLY = 'monthly';

    const AGGREGATION_YEARLY = 'yearly';

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product
     */
    public $_productResource;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Helper
     */
    public $_salesResourceHelper;

    /**
     * Ignored product types list
     *
     * @var array
     */
    public $ignoredProductTypes = [
        \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE => \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE,
    ];

    /**
     * Customers constructor.
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Reports\Model\FlagFactory $reportsFlagFactory
     * @param \Magento\Framework\Stdlib\DateTime\Timezone\Validator $timezoneValidator
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Magento\Catalog\Model\ResourceModel\Product $productResource
     * @param \Magento\Sales\Model\ResourceModel\Helper $salesResourceHelper
     * @param null $connectionName
     * @param array $ignoredProductTypes
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Reports\Model\FlagFactory $reportsFlagFactory,
        \Magento\Framework\Stdlib\DateTime\Timezone\Validator $timezoneValidator,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Catalog\Model\ResourceModel\Product $productResource,
        \Magento\Sales\Model\ResourceModel\Helper $salesResourceHelper,
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
        $this->_productResource = $productResource;
        $this->_salesResourceHelper = $salesResourceHelper;
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
    protected function _construct()
    {
        $this->_init('brainacts_salesrep_report_customer', 'id');
    }

    /**
     * @param $orderId
     * @param $data
     * @return $this
     * @throws \Exception
     */
    public function aggregate($orderId, $data)
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
}
