<?php
namespace Designnbuy\Productattach\Controller\Adminhtml\Fileicon;

class Edit extends \Designnbuy\Productattach\Controller\Adminhtml\Fileicon
{

    protected $resultPageFactory;

    /** @var \Designnbuy\Productattach\Model\FileiconFactory */
    private $fileiconFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Designnbuy\Productattach\Model\FileiconFactory $fileiconFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Designnbuy\Productattach\Model\FileiconFactory $fileiconFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->fileiconFactory = $fileiconFactory;
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
        $id = $this->getRequest()->getParam('fileicon_id');
        $model = $this->fileiconFactory->create();
        
        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This Fileicon no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->_coreRegistry->register('designnbuy_productattach_fileicon', $model);
        
        // 5. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit Fileicon') : __('New Fileicon'),
            $id ? __('Edit Fileicon') : __('New Fileicon')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Fileicons'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? $model->getIconExt() : __('New Fileicon'));
        return $resultPage;
    }
}
