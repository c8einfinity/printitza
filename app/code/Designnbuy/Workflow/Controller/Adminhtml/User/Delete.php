<?php


namespace Designnbuy\Workflow\Controller\Adminhtml\User;

class Delete extends \Magento\Backend\App\Action
{
    /**
     * User model factory
     *
     * @var \Magento\User\Model\UserFactory
     */
    protected $_userFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\User\Model\UserFactory $userFactory
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_userFactory = $userFactory;
        parent::__construct($context);
    }
    
    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create('Designnbuy\Workflow\Model\User');

                $model->load($id);

                $userModel = $this->_userFactory->create();
                $userModel->setId($model->getUserId());
                $userModel->delete();
                
                $model->delete();
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the User.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a User to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
