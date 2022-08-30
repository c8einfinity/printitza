<?php
namespace Designnbuy\Productattach\Controller\Adminhtml\Fileicon;

class Delete extends \Designnbuy\Productattach\Controller\Adminhtml\Fileicon
{

    /** @var \Designnbuy\Productattach\Model\FileiconFactory */
    private $fileiconFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Designnbuy\Productattach\Model\FileiconFactory $fileiconFactory
    ) {
        parent::__construct($context, $coreRegistry);
        $this->fileiconFactory = $fileiconFactory;
    }

    /**
     * Check admin permissions for this controller
     *
     * @return boolean
     */
    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Designnbuy_Productattach::delete');
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
        $id = $this->getRequest()->getParam('fileicon_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->fileiconFactory->create();
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the Fileicon.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['fileicon_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a Fileicon to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
