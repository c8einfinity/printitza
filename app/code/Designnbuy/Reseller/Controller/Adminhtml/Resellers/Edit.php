<?php
namespace Designnbuy\Reseller\Controller\Adminhtml\Resellers;

use Magento\Backend\App\Action;

class Edit extends \Magento\Backend\App\Action
{
    protected $_coreRegistry = null;

    protected $resultPageFactory;

    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
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
        $resultPage->getConfig()->getTitle()->prepend(__('Edit Resellers'));
        $resultPage->getConfig()->getTitle()
            ->prepend($model->getId() ? $model->getTitle() : __('New Resellers'));

        return $resultPage;
    }
}
