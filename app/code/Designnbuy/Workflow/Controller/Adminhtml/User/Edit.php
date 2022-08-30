<?php


namespace Designnbuy\Workflow\Controller\Adminhtml\User;

class Edit extends \Designnbuy\Workflow\Controller\Adminhtml\User
{

    protected $resultPageFactory;

    /**
     * User model factory
     *
     * @var \Magento\User\Model\UserFactory
     */
    protected $_userFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\User\Model\UserFactory $userFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_userFactory = $userFactory;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Edit action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('id');
        $model = $this->_objectManager->create('Designnbuy\Workflow\Model\User');
        $adminUserModel = $this->_userFactory->create();
        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This Workflow User no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            } else {
                $adminUserModel->load($model->getUserId());
                $this->_coreRegistry->register('designnbuy_workflow_admin_user', $adminUserModel);
            }
        }
        $this->_coreRegistry->register('designnbuy_workflow_user', $model);
        
        // 5. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit User') : __('New Workflow User'),
            $id ? __('Edit User') : __('New User')
        );
        
        $resultPage->getConfig()->getTitle()->prepend(__('Workflow Users'));
        $resultPage->getConfig()->getTitle()->prepend($adminUserModel->getUserId() ? $adminUserModel->getName() : __('New Workflow User'));
        return $resultPage;
    }
}
