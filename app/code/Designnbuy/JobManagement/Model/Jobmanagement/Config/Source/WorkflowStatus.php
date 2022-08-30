<?php

namespace Designnbuy\JobManagement\Model\Jobmanagement\Config\Source;

use Designnbuy\Workflow\Model\ResourceModel\Status\CollectionFactory as CollectionFactory;
/**
 * Used in creating options for commission type config value selection
 */

class WorkflowStatus implements \Magento\Framework\Option\ArrayInterface
{
    
    protected $collectionFactory;

    protected $authSession;

    protected $userFactory;

    protected $roleFactory;

    protected $_productFactory;

    protected $options;

    protected $_request;

    protected $_jobManagementFactory;

    /**
     * @var \Designnbuy\Workflow\Helper\Data
     */
    public $_workflowHelper;

    /**
     * @param CollectionFactory $collectionFactory
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Designnbuy\Workflow\Model\UserFactory $userFactory
     * @param \Designnbuy\Workflow\Model\RoleFactory $roleFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Designnbuy\JobManagement\Model\JobmanagementFactory $jobManagementFactory
     * @param array $data
     */

    public function __construct(
        CollectionFactory $collectionFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Designnbuy\Workflow\Model\UserFactory $userFactory,
        \Designnbuy\Workflow\Model\RoleFactory $roleFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Framework\App\RequestInterface $request,
        \Designnbuy\JobManagement\Model\JobmanagementFactory $jobManagementFactory,
        \Designnbuy\Workflow\Helper\Data $workflowHelper
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->authSession = $authSession;
        $this->userFactory = $userFactory;
        $this->roleFactory = $roleFactory;
        $this->_productFactory = $productFactory;
        $this->_request = $request;
        $this->_jobManagementFactory = $jobManagementFactory;
        $this->_workflowHelper = $workflowHelper;
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

    public function getUpdateStatuses()
    {
        $currentWorkFlowRole = $this->getCurrentUserWorkflowRole();
        $updateStatuses = [];
        if($currentWorkFlowRole){
            $updateStatuses = $currentWorkFlowRole->getUpdateStatuses();
        }
        return $updateStatuses;
    }

    public function toOptionArray()
    {
        $id = $this->_request->getParam('id');
        $jobManagement = $this->_jobManagementFactory->create()->load($id);
        $_item = $jobManagement->getItemId();
        $productId = $jobManagement->getProductId();

        $product = $this->_productFactory->create()->load($productId);
        $productWorkFlowGroupId = $product->getWorkflowGroup();

        if(!is_array($productWorkFlowGroupId)) {
            $groupIdsArray[] = $productWorkFlowGroupId;
        } else {
            $groupIdsArray = $productWorkFlowGroupId;
        }

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
            $collection = $this->collectionFactory->create();
            $collection->addFieldToFilter('status', 1);
            $collection->addFieldToFilter('status_id', $workflowStatus);
            $collection->getSelect()->order('position', 'ASC');
        }else{
            $collection = $this->collectionFactory->create();
            $collection->addFieldToFilter('group', ['finset' => $groupIdsArray]);
            $collection->addFieldToFilter('status', 1);
            $collection->getSelect()->order('position', 'ASC');
            $updateStatuses = $this->getUpdateStatuses();
        }

        if ($this->options === null) {
            $this->options = [['label' => __('Please select'), 'value' => 0]];
            foreach ($collection as $status) {
                $this->options[] = ['value' => $status['status_id'], 'label' => $status['title'],];
            }        
        }
        return $this->options;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
    */
    public function toArray()
    {
        $array = [];
        foreach ($this->toOptionArray() as $item) {
            $array[$item['value']] = $item['label'];
        }
        return $array;
    }
}
