<?php


namespace Designnbuy\Vendor\Controller\Adminhtml\Transaction;

use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Backend\App\Action
{

    protected $dataPersistor;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Designnbuy\Vendor\Model\ResourceModel\Transaction\CollectionFactory $collectionFactory
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        $data = $this->getRequest()->getPostValue();
        $vendorId = $this->getRequest()->getParam('vendor_id');

        if ($data) {
            $id = $this->getRequest()->getParam('transaction_id');
        
            $model = $this->_objectManager->create('Designnbuy\Vendor\Model\Transaction')->load($id);

            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Transaction no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }


            $creditCollection = $this->collectionFactory->create();
            $creditCollection->addFieldToFilter('vendor_id',$vendorId);
            //$creditCollection->addFieldToFilter('type','Credit');
            $creditCollection->addFieldToFilter('order_increment_id',['neq' => 'NULL']);
            $creditCollection->addExpressionFieldToSelect('credit_amount', 'SUM({{amount}})', 'amount');


            $debitCollection = $this->collectionFactory->create();
            $debitCollection->addFieldToFilter('vendor_id',$vendorId);
            //$debitCollection->addFieldToFilter('type','Debit');
            $debitCollection->addFieldToFilter('order_increment_id',array('null' => true));
            $debitCollection->addExpressionFieldToSelect('debit_amount', 'SUM({{amount}})', 'amount');

            $balanceAmount = $creditCollection->getFirstItem()->getCreditAmount() - $debitCollection->getFirstItem()->getDebitAmount();

            if($balanceAmount > 0){
                if(isset($data) && !empty($data) && array_key_exists('amount',$data)){
                    if($data['amount'] > $balanceAmount){
                        $this->messageManager->addErrorMessage(__('You can not transfer more than net amount value.'));
                        return $resultRedirect->setPath('*/*/', ['vendor_id' => $vendorId]);
                    }
                }
            }

            $data['vendor_id'] = $vendorId;
            $data['type'] = 'Debit';

            $model->setData($data);
        
            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the Transaction.'));
                $this->dataPersistor->clear('designnbuy_vendor_transaction');
        
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['transaction_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/', ['vendor_id' => $vendorId]);
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Transaction.'));
            }
        
            $this->dataPersistor->set('designnbuy_vendor_transaction', $data);
            return $resultRedirect->setPath('*/*/edit', ['transaction_id' => $this->getRequest()->getParam('transaction_id'), 'vendor_id' => $vendorId]);
        }
        return $resultRedirect->setPath('*/*/', ['vendor_id' => $vendorId]);
    }
}
