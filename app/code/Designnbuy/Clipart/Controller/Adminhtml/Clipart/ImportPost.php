<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Clipart\Controller\Adminhtml\Clipart;

use Magento\Framework\Controller\ResultFactory;

class ImportPost extends \Designnbuy\Clipart\Controller\Adminhtml\Clipart
{
    /**
     * import action from import/export clipart
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        if ($this->getRequest()->isPost() && !empty($_FILES['import_clipart_file']['tmp_name'])) {
            try {
                /** @var $importHandler \Designnbuy\Clipart\Model\Clipart\CsvImportHandler */
                $importHandler = $this->_objectManager->create('Designnbuy\Clipart\Model\Clipart\CsvImportHandler');
                $importHandler->importFromCsvFile($this->getRequest()->getFiles('import_clipart_file'));

                $this->messageManager->addSuccess(__('The clipart has been imported.'));
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError(__('Invalid file upload attempt'));
            }
        } else {
            $this->messageManager->addError(__('Invalid file upload attempt'));
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRedirectUrl());
        return $resultRedirect;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(
            'Designnbuy_Clipart::elements'
        ) || $this->_authorization->isAllowed(
            'Designnbuy_Clipart::import_export'
        );

    }
}
