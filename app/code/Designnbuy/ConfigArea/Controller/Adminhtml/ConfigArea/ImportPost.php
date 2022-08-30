<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\ConfigArea\Controller\Adminhtml\ConfigArea;

use Magento\Framework\Controller\ResultFactory;

class ImportPost extends \Designnbuy\ConfigArea\Controller\Adminhtml\ConfigArea
{
    /**
     * import action from import/export configarea
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        if ($this->getRequest()->isPost() && !empty($_FILES['import_configarea_file']['tmp_name'])) {
            try {
                /** @var $importHandler \Designnbuy\ConfigArea\Model\ConfigArea\CsvImportHandler */
                $importHandler = $this->_objectManager->create('Designnbuy\ConfigArea\Model\ConfigArea\CsvImportHandler');
                $importHandler->importFromCsvFile($this->getRequest()->getFiles('import_configarea_file'));

                $this->messageManager->addSuccess(__('The Design Area has been imported.'));
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
            'Designnbuy_ConfigArea::elements'
        ) || $this->_authorization->isAllowed(
            'Designnbuy_ConfigArea::import_export'
        );

    }
}
