<?php

namespace Designnbuy\Reseller\Controller\Adminhtml\Resellers;

use Magento\Backend\App\Action;
use Magento\TestFramework\ErrorLog\Logger;

class Importproducts extends \Magento\Backend\App\Action
{
    protected $_coreRegistry = null;

    protected $resultPageFactory;

    public function __construct(
        \Magento\User\Model\User $user,
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry        
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->_user = $user;
        parent::__construct($context);
    }

    protected function _isAllowed()
    {
        return true;
    }

    protected function _initAction()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Designnbuy_Reseller::manage_resellers')
            ->addBreadcrumb(__('Manage Resellers'), __('Manage Resellers'))
            ->addBreadcrumb(__('Resellers'), __('Resellers'));
        return $resultPage;
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('reseller_id');
        $model = $this->_objectManager->create('Designnbuy\Reseller\Model\Resellers');

        if ($id) {
            $model->load($id);

            $userId = $model->getUserId();
            $user = $this->_user;
            if(isset($userId)):
                $user->load($userId);
                $userName = $user->getUsername();
            endif;

            if (!$model->getId()) {
                $this->messageManager->addError(__('This item no longer exists.'));
                /** \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
       
        $this->_coreRegistry->register('resellers', $model);

        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(__('Resellers'), __('Resellers'));
        $resultPage->addBreadcrumb(
            $id ? __('Edit Resellers') : __('New Resellers'),
            $id ? __('Edit Resellers') : __('New Resellers')
        );
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? __('Edit Reseller %1', $userName) : __('New Resellers'));

        return $resultPage;
    }
}