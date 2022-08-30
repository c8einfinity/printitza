<?php

namespace Designnbuy\JobManagement\Block\Adminhtml\Grid\Column;
use Designnbuy\Workflow\Model\ResourceModel\Status\CollectionFactory as StatusCollectionFactory;

/**
 * Admin Writer book grid statuses
 */
class WorkflowStatus extends \Magento\Backend\Block\Widget\Grid\Column
{
    /**
     * @param StatusCollectionFactory $statusCollectionFactory
     */
    protected $statusCollectionFactory;

    protected $authSession;

    protected $userFactory;

    protected $roleFactory;

    protected $_productFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\Product\OptionFactory $optionFactory
     * @param array $data
     */
    public function __construct(
        StatusCollectionFactory $statusCollectionFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Designnbuy\Workflow\Model\UserFactory $userFactory,
        \Designnbuy\Workflow\Model\RoleFactory $roleFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory
    ) {
        $this->statusCollectionFactory = $statusCollectionFactory;
        $this->authSession = $authSession;
        $this->userFactory = $userFactory;
        $this->roleFactory = $roleFactory;
        $this->_productFactory = $productFactory;
    }

    public function getFrameCallback()
    {
        return [$this, 'decorateStatus'];
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

    public function decorateStatus($value, $row, $column, $isExport)
    {
        /** @var \Magento\Sales\Block\Adminhtml\Items\Column\DefaultColumn $block */
        $_item = $row->getItemId();
        $productId = $row->getProductId();
        $product = $this->_productFactory->create()->load($productId);
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
        $updateStatuses = $this->getUpdateStatuses();

        $cell = "";
        if($collection->count() > 0):
        $cell = '<div class="admin__field-control">';        
            $cell .= '<select class="admin__control-select" id="item_status_'.$_item.'" name="item_status_'.$_item.'">';
                $cell .= '<option value="">'."--Select Status--".'</option>';
                foreach($collection as $status):
                    if($this->getCurrentWorkFlowUser()):
                        if((in_array($status->getId(), $updateStatuses))):                            
                            if($row->getWorkflowStatusId() == $status->getStatusId()):
                                $select = 'selected="selected"';
                            else:
                                $select = '';
                            endif;
                            $cell .= '<option value="'.$status->getStatusId().'" '.$select.' >'.$status->getTitle().'</option>';
                        endif;
                    else:   
                        //echo $row->getWorkflowStatusId() .'<br/>'. $status->getStatusId().'<br> --<br>';
                        if($row->getWorkflowStatusId() == $status->getStatusId()):
                            $select = 'selected="selected"';
                        else:
                            $select = '';
                        endif;
                    $cell .= '<option value="'.$status->getStatusId().'" '.$select.' >'.$status->getTitle().'</option>';
                    endif;
                endforeach;
            $cell .= '</select>';
        $cell .= '</div>';
        else:
        $cell .= 'N/A';
        endif; 
        return $cell;
    }
}
