<?php
         
namespace Designnbuy\JobManagement\Block\Adminhtml\Sales\Order\Items\Column;

use Designnbuy\JobManagement\Model\ResourceModel\Jobmanagement\JobCollectionFactory as JobCollectionFactory;
use Designnbuy\Workflow\Model\ResourceModel\Status\CollectionFactory as StatusCollectionFactory;
/**
 * Sales Order items name column renderer
 */
class Jobstart extends \Magento\Sales\Block\Adminhtml\Items\Column\DefaultColumn
{
    /**
     * @param StatusCollectionFactory $statusCollectionFactory
     */
    protected $statusCollectionFactory;

    /**
     * @param JobCollectionFactory $jobCollectionFactory
     */
    protected $jobCollectionFactory;

    protected $authSession;

    protected $userFactory;

    protected $roleFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\Product\OptionFactory $optionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\Product\OptionFactory $optionFactory,
        JobCollectionFactory $jobCollectionFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Designnbuy\Workflow\Model\UserFactory $userFactory,
        \Designnbuy\Workflow\Model\RoleFactory $roleFactory,
        \Designnbuy\JobManagement\Model\JobmanagementFactory $jobManagementFactory,
        StatusCollectionFactory $statusCollectionFactory,
        array $data = []
    ) {
        $this->jobCollectionFactory = $jobCollectionFactory;
        $this->authSession = $authSession;
        $this->userFactory = $userFactory;
        $this->roleFactory = $roleFactory;
        $this->_jobManagementFactory = $jobManagementFactory;
        $this->statusCollectionFactory = $statusCollectionFactory;
        parent::__construct($context, $stockRegistry, $stockConfiguration, $registry, $optionFactory, $data);
    }

    public function getSubmitUrl()
    {
        $item = $this->getItem()->getId();
        $vendorId = $this->getItem()->getVendorId();
        return $this->getUrl('jobmanagement/*/jobstart', ['item_id' => $this->getItem()->getId(), 'order_id' => $this->getItem()->getOrderId(), 'product_id' => $this->getItem()->getProductId(), 'vendor_user_id' => $vendorId]);
    }

    public function getJobViewUrl()
    {
        $item = $this->getItem()->getId();        
        $_jobManagement = $this->_jobManagementFactory->create()->load($this->getItem()->getId(), 'item_id');
        $entityId = $_jobManagement->getEntityId();
        return $this->getUrl('jobmanagement/jobmanagement/edit', ['id' => $entityId, 'order_id' => $this->getItem()->getOrderId()]);
    }

    public function getJobItemCollection()
    {
        $item = $this->getItem();    
        $collection = $this->jobCollectionFactory->create();        
        $collection->addFieldToFilter('item_id', $item->getId());
        $collection->addFieldToFilter('order_id', $item->getOrderId());
        return $collection;
    }

    public function getWorkFlowItemStatuses()
    {
        $item = $this->getItem();
        $product = $item->getProduct();
        $productWorkFlowGroupId = $product->getWorkflowGroup();

        if(!is_array($productWorkFlowGroupId)) {
            $groupIdsArray[] = $productWorkFlowGroupId;
        } else {
            $groupIdsArray = $productWorkFlowGroupId;
        }

        $collection = $this->statusCollectionFactory->create();
        $collection->addFieldToFilter('group', ['finset' => $groupIdsArray]);
        $collection->addFieldToFilter('status', 1);
        $collection->getSelect()->order('position', 'ASC');
        return $collection;
    }
}
