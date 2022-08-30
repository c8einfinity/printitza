<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Customer\Model\ResourceModel\Problem;

use Magento\Customer\Api\CustomerRepositoryInterface as CustomerRepository;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Customer problems collection
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * True when designs info joined
     *
     * @var bool
     */
    protected $_designsInfoJoinedFlag = false;

    /**
     * True when grouped
     *
     * @var bool
     */
    protected $_problemGrouped = false;

    /**
     * Customer collection factory
     *
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
     */
    protected $_customerCollectionFactory;

    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    /**
     * Customer View Helper
     *
     * @var \Magento\Customer\Helper\View
     */
    protected $_customerView;

    /**
     * checks if customer data is loaded
     *
     * @var boolean
     */
    protected $_loadCustomersDataFlag = false;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param CustomerRepository $customerRepository
     * @param \Magento\Customer\Helper\View $customerView
     * @param \Magento\Framework\DB\Adapter\AdapterInterface $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        CustomerRepository $customerRepository,
        \Magento\Customer\Helper\View $customerView,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->customerRepository = $customerRepository;
        $this->_customerView = $customerView;
    }

    /**
     * Define resource model and model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Designnbuy\Customer\Model\Problem', 'Designnbuy\Customer\Model\ResourceModel\Problem');
    }

    /**
     * Set customer loaded status
     *
     * @param bool $flag
     * @return $this
     */
    protected function _setIsLoaded($flag = true)
    {
        if (!$flag) {
            $this->_loadCustomersDataFlag = false;
        }
        return parent::_setIsLoaded($flag);
    }
    /**
     * Adds designs info
     *
     * @return $this
     */
    public function addDesignInfo()
    {
        $this->getSelect()->joinLeft(
            ['design' => $this->getTable('designnbuy_customer_design')],
            'main_table.design_id = design.design_id',
            ['design_email', 'customer_id', 'design_status']
        );
        $this->addFilterToMap('design_id', 'main_table.design_id');
        $this->_designsInfoJoinedFlag = true;

        return $this;
    }

    /**
     * Adds queue info
     *
     * @return $this
     */
    public function addQueueInfo()
    {
        $this->getSelect()->joinLeft(
            ['queue' => $this->getTable('designnbuy_customer_queue')],
            'main_table.queue_id = queue.queue_id',
            ['queue_start_at', 'queue_finish_at']
        )->joinLeft(
            ['template' => $this->getTable('customer_template')],
            'queue.template_id = template.template_id',
            ['template_subject', 'template_code', 'template_sender_name', 'template_sender_email']
        );
        return $this;
    }

    /**
     * Loads customers info to collection
     *
     * @return void
     */
    protected function _addCustomersData()
    {
        if ($this->_loadCustomersDataFlag) {
            return;
        }
        $this->_loadCustomersDataFlag = true;
        foreach ($this->getItems() as $item) {
            if ($item->getCustomerId()) {
                $customerId = $item->getCustomerId();
                try {
                    $customer = $this->customerRepository->getById($customerId);
                    $problems = $this->getItemsByColumnValue('customer_id', $customerId);
                    $customerName = $this->_customerView->getCustomerName($customer);
                    foreach ($problems as $problem) {
                        $problem->setCustomerName($customerName)
                            ->setCustomerFirstName($customer->getFirstName())
                            ->setCustomerLastName($customer->getLastName());
                    }
                } catch (NoSuchEntityException $e) {
                    // do nothing if customer is not found by id
                }
            }
        }
    }

    /**
     * Loads collecion and adds customers info
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @return $this
     */
    public function load($printQuery = false, $logQuery = false)
    {
        parent::load($printQuery, $logQuery);
        if ($this->_designsInfoJoinedFlag) {
            $this->_addCustomersData();
        }
        return $this;
    }
}
