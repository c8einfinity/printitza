<?php
/**
 * Copyright © 2019 Design 'N' Buy. All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 * ALWAYS DO BETTER @a
 */

namespace Designnbuy\JobManagement\Model\ResourceModel\Jobmanagement;

/**
 * Writer collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * @var int
     */
    protected $_storeId;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $customer;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var \Designnbuy\Workflow\Helper\Data
     */
    public $_workflowHelper;

    /**
     * @var \Designnbuy\Vendor\Helper\Data
     */
    public $_vendorHelper;
    

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param Magento\Store\Model\StoreManagerInterface $storeManager
     * @param null|\Zend_Db_Adapter_Abstract $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     */
    public function __construct(        
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Designnbuy\Workflow\Helper\Data $workflowHelper,
        \Designnbuy\Vendor\Helper\Data $vendorHelper
    ) {
        $this->_date = $date;
        $this->_customerFactory = $customerFactory;
        $this->_storeManager = $storeManager;
        $this->_workflowHelper = $workflowHelper;
        $this->_vendorHelper = $vendorHelper;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * Constructor
     * Configures collection
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Designnbuy\JobManagement\Model\Jobmanagement', 'Designnbuy\JobManagement\Model\ResourceModel\Jobmanagement');
        $this->_map['fields']['entity_id'] = 'main_table.entity_id';        
    }

    /**
     * Add field filter to collection
     *
     * @param string|array $field
     * @param null|string|array $condition
     * @return $this
     */

    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()
            ->joinLeft(
                ['sales_item' => $this->getTable('sales_order_item')],
                'main_table.item_id = sales_item.item_id',
                ['name', 'workflow_status']
            )->joinLeft(
                ['order' => $this->getTable('sales_order')],
                'main_table.order_id = order.entity_id',
                ['increment_id','status']
            );

        ## Access allowed for Workflow user @13
        if($this->_workflowHelper->getWorkflowUser()) {
            $workFlowRole = $this->_workflowHelper->getWorkflowUserRole();             
            $userViewStatus     = $workFlowRole->getViewStatus();
            $userUpdateStatus   = $workFlowRole->getUpdateStatus();
            $workflowViewStatusArray = array();
            $workflowUpdateStatusArray = array();
            if(isset($userViewStatus) && $userViewStatus != ""){
                $workflowViewStatusArray = explode(",", $userViewStatus);
            }
            if(isset($userUpdateStatus) && $userUpdateStatus != ""){
                $workflowUpdateStatusArray = explode(",", $userUpdateStatus);
            }
            $workflowStatus = array_merge($workflowViewStatusArray, $workflowUpdateStatusArray);                
            $this->addFieldToFilter('workflow_status_id', $workflowStatus);
        }
        ## End @13

        ## Access allowed for Vendor user @13
        if($this->_vendorHelper->getVendorUser()) {            
            $vendorUser = $this->_vendorHelper->getVendorUser();
            $vendorUserId = $vendorUser->getUserId();
            if($vendorUserId != ""){
                $this->addFieldToFilter('vendor_user_id', $vendorUserId);
            }
        }
        ## End @13
    }

    public function addFieldToFilter($field, $condition = null)
    {
        return parent::addFieldToFilter($field, $condition);
    }
    
    /**
     * Add "include in recent" filter to collection
     * @return $this
     */
    public function addRecentFilter()
    {
      return $this->addFieldToFilter('include_in_recent', 1);
    }
    
    /**
     * Add writer filter to collection
     * @param array|int|string  $category
     * @return $this
     */
    public function addJobmanagementFilter($jobmanagementIds)
    {
        if (!is_array($jobmanagementIds)) {
            $jobmanagementIds = explode(',', $jobmanagementIds);
            foreach ($jobmanagementIds as $key => $id) {
                $id = trim($id);
                if (!$id) {
                    unset($jobmanagementIds[$key]);
                }
            }
        }

        if (!count($jobmanagementIds)) {
            $jobmanagementIds = [0];
        }

        $this->addFieldToFilter(
            'entity_id',
            ['in' => $jobmanagementIds]
        );
    }

    /**
     * Add search filter to collection
     * @param string $term
     * @return $this
     */
    public function addSearchFilter($term)
    {
        $this->addFieldToFilter(
            ['is_active'],
            [
                ['like' => '%' . $term . '%'],
                ['like' => '%' . $term . '%'],
                ['like' => '% ' . $term . ' %']
            ]
        );
        return $this;
    }

    /**
     * Add is_active filter to collection
     * @return $this
     */
    public function addActiveFilter() {
        return $this->addFieldToFilter('main_table.is_active', 1);
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

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            $this->options = [['label' => __('Please select'), 'value' => 0]];


            foreach ($this as $item) {
                $this->options[] = [
                    'label' => $item->getName(),
                    'value' => $item->getId(),
                ];
            }
        }
        return $this->options;
    }
}
