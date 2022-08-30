<?php


namespace Designnbuy\Vendor\Controller\Adminhtml\Transaction;

class Edit extends \Designnbuy\Vendor\Controller\Adminhtml\Transaction
{

    protected $resultPageFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
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
        $id = $this->getRequest()->getParam('transaction_id');
        $vendorId = $this->getRequest()->getParam('vendor_id');
        
        $model = $this->_objectManager->create('Designnbuy\Vendor\Model\Transaction');
        
        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This Transaction no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->_coreRegistry->register('designnbuy_vendor_transaction', $model);
        $this->_coreRegistry->register('vendor_id', $vendorId);

        // 5. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit Transaction') : __('New Transaction'),
            $id ? __('Edit Transaction') : __('New Transaction')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Transactions'));
        $resultPage->getConfig()->getTitle()->prepend($model->getTransactionId() ? $model->getData('title') : __('New Transaction'));
        return $resultPage;
    }
}
