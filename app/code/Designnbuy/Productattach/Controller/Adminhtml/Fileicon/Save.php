<?php
namespace Designnbuy\Productattach\Controller\Adminhtml\Fileicon;

use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Backend\App\Action
{

    protected $dataPersistor;

    /** @var \Designnbuy\Productattach\Model\FileiconFactory */
    private $fileiconFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     * @param \Designnbuy\Productattach\Model\FileiconFactory $fileiconFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Designnbuy\Productattach\Model\FileiconFactory $fileiconFactory
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->fileiconFactory = $fileiconFactory;
        parent::__construct($context);
    }

    /**
     * Check admin permissions for this controller
     *
     * @return boolean
     */
    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Designnbuy_Productattach::save');
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
        if ($data) {
            $id = $this->getRequest()->getParam('fileicon_id');
        
            $model = $this->fileiconFactory->create()->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Fileicon no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }
            
            $data = $this->_filterFileiconData($data);
            $model->setData($data);
        
            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the Fileicon.'));
                $this->dataPersistor->clear('designnbuy_productattach_fileicon');
        
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['fileicon_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Fileicon.'));
            }
        
            $this->dataPersistor->set('designnbuy_productattach_fileicon', $data);
            return $resultRedirect->setPath('*/*/edit', ['fileicon_id' => $this->getRequest()->getParam('fileicon_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    public function _filterFileiconData(array $rawData)
    {
        $data = $rawData;
        if (isset($data['icon_image'][0]['name'])) {
            $data['icon_image'] = $data['icon_image'][0]['name'];
        } else {
            $data['icon_image'] = null;
        }
        return $data;
    }
}
