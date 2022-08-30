<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Workflow\Block\Adminhtml\Sales\Order\Items\Column;

use Designnbuy\Workflow\Model\ResourceModel\Status\CollectionFactory as StatusCollectionFactory;

/**
 * Sales Order items name column renderer
 */
class WorkflowStatus extends \Magento\Sales\Block\Adminhtml\Items\Column\DefaultColumn
{
    /**
     * @param StatusCollectionFactory $statusCollectionFactory
     */
    protected $statusCollectionFactory;

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
        StatusCollectionFactory $statusCollectionFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Designnbuy\Workflow\Model\UserFactory $userFactory,
        \Designnbuy\Workflow\Model\RoleFactory $roleFactory,
        array $data = []
    ) {
        $this->statusCollectionFactory = $statusCollectionFactory;
        $this->authSession = $authSession;
        $this->userFactory = $userFactory;
        $this->roleFactory = $roleFactory;
        parent::__construct($context, $stockRegistry, $stockConfiguration, $registry, $optionFactory, $data);
    }


    public function getSubmitUrl()
    {
        $item = $this->getItem()->getId();
        return $this->getUrl('designnbuy_workflow/*/changeStatus', ['item_id' => $this->getItem()->getId()]);
    }

    public function getCurrentUser()
    {
        return $this->authSession->getUser();
    }

    public function getCurrentWorkFlowUser()
    {
        $workFlowUser = $this->userFactory->create()->loadByUserId($this->getCurrentUser()->getUserId());
        if($workFlowUser && $workFlowUser->getId()){
            return $workFlowUser;
        }
        return;
    }

    public function getCurrentUserWorkflowRole()
    {
        $workFlowUser = $this->getCurrentWorkFlowUser();
        if($workFlowUser && $workFlowUser->getId()){
            return $this->roleFactory->create()->load($workFlowUser->getRoleId());
        }
        return;
    }

    public function getViewStatuses()
    {
        $currentWorkFlowRole = $this->getCurrentUserWorkflowRole();
        $viewStatuses = [];
        if($currentWorkFlowRole) {
            $viewStatuses = $currentWorkFlowRole->getViewStatuses();
        }
        return $viewStatuses;
    }

    public function getUpdateStatuses()
    {
        $currentWorkFlowRole = $this->getCurrentUserWorkflowRole();
        $updateStatuses = [];
        if($currentWorkFlowRole){
            $updateStatuses = $currentWorkFlowRole->getUpdateStatuses();
        }
        return $updateStatuses;
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
