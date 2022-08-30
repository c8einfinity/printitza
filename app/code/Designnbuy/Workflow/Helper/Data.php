<?php
/**
 * Customer attribute data helper
 */

namespace Designnbuy\Workflow\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    protected $authSession;

    protected $userFactory;

    protected $roleFactory;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Designnbuy\Workflow\Model\UserFactory $userFactory,
        \Designnbuy\Workflow\Model\RoleFactory $roleFactory

    ) {
        parent::__construct($context);
        $this->authSession = $authSession;
        $this->userFactory = $userFactory;
        $this->roleFactory = $roleFactory;
    }

    public function getProductWorkFlowGroup()
    {
        
    }

    public function getCurrentUser()
    {
        return $this->authSession->getUser();
    }

    public function getWorkflowUser()
    {
        $user = $this->getCurrentUser();
        if($user){
            $workFlowUser = $this->userFactory->create()->loadByUserId($user->getUserId());
            if($workFlowUser->getUserId()) {
                return $workFlowUser;
            }
        }

        return false;
    }

    public function getWorkflowUserRole()
    {
        $workFlowUser = $this->getWorkflowUser();
        if($workFlowUser){
            return $this->roleFactory->create()->load($workFlowUser->getRoleId());
        }
        return false;
    }

    public function getWorkFlowUserViewStatuses()
    {
        $workFlowRole = $this->getWorkflowUserRole();
        if($workFlowRole){
            return $workFlowRole->getViewStatus();
        }
        return false;
    }

    public function getWorkFlowUserUpdateStatuses()
    {
        $workFlowRole = $this->getWorkflowUserRole();
        if($workFlowRole){
            return $workFlowRole->getUpdateStatus();
        }
        return false;
    }

}
