<?php


namespace Designnbuy\Vendor\Controller\Adminhtml\Transaction;

class Delete extends \Designnbuy\Vendor\Controller\Adminhtml\Transaction
{

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
        $id = $this->getRequest()->getParam('transaction_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create('Designnbuy\Vendor\Model\Transaction');
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the Transaction.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['transaction_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a Transaction to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
