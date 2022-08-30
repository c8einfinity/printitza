<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace BrainActs\SalesRepresentative\Model\ResourceModel;

/**
 * Class AbstractCollection
 * @author BrainActs Core Team <support@brainacts.com>
 */
//@codingStandardsIgnoreFile
abstract class AbstractCollection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;

    /**
     * @var \Magento\Framework\EntityManager\MetadataPool
     */
    public $metadataPool;

    /**
     * AbstractCollection constructor.
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\EntityManager\MetadataPool $metadataPool
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\EntityManager\MetadataPool $metadataPool,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
    
        $this->storeManager = $storeManager;
        $this->metadataPool = $metadataPool;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * Perform operations after collection load
     *
     * @param string $tableName
     * @param string|null $linkField
     * @return void
     */
    protected function performAfterLoad($tableName, $linkField)
    {
        $linkedIds = $this->getColumnValues($linkField);

        if (count($linkedIds)) {
            $connection = $this->getConnection();
            $select = $connection->select()->from(['brainacts_salesrep_member_customer' => $this->getTable($tableName)])
                ->where('brainacts_salesrep_member_customer.' . $linkField . ' IN (?)', $linkedIds);
            $result = $connection->fetchAll($select);

            if ($result) {
                $customersData = [];
                foreach ($result as $customerData) {
                    $customersData[$customerData[$linkField]][] = $customerData['customer_id'];
                }

                foreach ($this as $item) {
                    $linkedId = $item->getData($linkField);
                    if (!isset($customersData[$linkedId])) {
                        continue;
                    }

                    $customerId = current($customersData[$linkedId]);

                    $item->setData('_first_customer_id', $customerId);

                    $item->setData('customer_id', $customersData[$linkedId]);
                }
            }
        }
    }

    /**
     * Add filter by customer
     *
     * @param int|array|
     * @return $this
     */
    abstract public function addCustomerFilter($customerId);

    /**
     * Add filter by product
     *
     * @param int|array|
     * @return $this
     */
    abstract public function addProductFilter($productId);

    /**
     * Perform adding filter by customer
     *
     * @param $customers
     */
    protected function performAddCustomerFilter($customers)
    {

        if (!is_array($customers)) {
            $customers = [$customers];
        }

        $this->addFilter('customer', ['in' => $customers], 'public');
    }

    /**
     * Perform adding filter by product
     *
     * @param $products
     */
    protected function performAddProductFilter($products)
    {

        if (!is_array($products)) {
            $products = [$products];
        }

        $this->addFilter('product', ['in' => $products], 'public');
    }

    /**
     * Join customer relation table if there is customer filter
     *
     * @param string $tableName
     * @param string|null $linkField
     * @return void
     */
    protected function joinCustomerRelationTable($tableName, $linkField)
    {
        if ($this->getFilter('customer')) {
            $this->getSelect()->join(
                ['customer_table' => $this->getTable($tableName)],
                'main_table.' . $linkField . ' = customer_table.' . $linkField,
                []
            )->group(
                'main_table.' . $linkField
            );
        }
        parent::_renderFiltersBefore();
    }

    /**
     * Join product relation table if there is product filter
     *
     * @param string $tableName
     * @param string|null $linkField
     * @return void
     */
    protected function joinProductRelationTable($tableName, $linkField)
    {
        if ($this->getFilter('product')) {
            $this->getSelect()->join(
                ['product_table' => $this->getTable($tableName)],
                'main_table.' . $linkField . ' = product_table.' . $linkField,
                []
            )->group(
                'main_table.' . $linkField
            );
        }
        parent::_renderFiltersBefore();
    }

    /**
     * Join product relation table if there is product filter
     *
     * @param string $tableName
     * @param string|null $linkField
     * @return void
     */
    protected function joinCustomerRequestRelationTable($tableName, $linkField)
    {
        if ($this->getFilter('firstname')) {
            $this->getSelect()->join(
                ['customer_table' => $this->getTable($tableName)],
                'main_table.' . $linkField . ' = customer_table.' . $linkField,
                []
            )->group(
                'main_table.' . $linkField
            );
        }
        parent::_renderFiltersBefore();
    }

    /**
     * Join order relation table if there is order filter
     *
     * @param string $tableName
     * @param string|null $linkField
     * @return void
     */
    protected function joinOrderRelationTable($tableName, $linkField)
    {

        if ($this->getFilter('order_id')) {
            $this->getSelect()->join(
                ['order_table' => $this->getTable($tableName)],
                'main_table.' . $linkField . ' = order_table.' . $linkField,
                []
            )->group(
                'main_table.' . $linkField
            );
        }

        parent::_renderFiltersBefore();
    }

    /**
     * Get SQL for get record count
     *
     * Extra GROUP BY strip added.
     *
     * @return \Magento\Framework\DB\Select
     */
    public function getSelectCountSql()
    {

        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(\Magento\Framework\DB\Select::GROUP);

        return $countSelect;
    }

    protected function _getAllIdsSelect($limit = null, $offset = null) //@codingStandardsIgnoreLine
    {
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(\Magento\Framework\DB\Select::ORDER);
        $idsSelect->reset(\Magento\Framework\DB\Select::LIMIT_COUNT);
        $idsSelect->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);
        $idsSelect->reset(\Magento\Framework\DB\Select::COLUMNS);
        $idsSelect->columns($this->getIdFieldName());
        $idsSelect->limit($limit, $offset);

        return $idsSelect;
    }

    protected function filterOrder($payment_method) //@codingStandardsIgnoreLine
    {
        $this->sales_order_table = "main_table";
        $this->sales_order_payment_table = $this->getTable("sales_order_payment");
        $this->getSelect()
            ->join(array('payment' =>$this->sales_order_payment_table), $this->sales_order_table . '.entity_id= payment.parent_id',
                array('payment_method' => 'payment.method',
                    'order_id' => $this->sales_order_table.'.entity_id'
                )
            );
        $this->getSelect()->where("payment_method=".$payment_method);
    }
}
