<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Font\Controller\Adminhtml\Font;

use Magento\Framework\Controller\ResultFactory;

class ImportPost extends \Designnbuy\Font\Controller\Adminhtml\Font
{
    /**
     * import action from import/export font
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        if ($this->getRequest()->isPost() && !empty($_FILES['import_font_file']['tmp_name'])) {
            try {
                /** @var $importHandler \Designnbuy\Font\Model\Font\CsvImportHandler */
                $importHandler = $this->_objectManager->create('Designnbuy\Font\Model\Font\CsvImportHandler');
                $importHandler->importFromCsvFile($this->getRequest()->getFiles('import_font_file'));

                $this->messageManager->addSuccess(__('The font has been imported.'));
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
            'Designnbuy_Font::elements'
        ) || $this->_authorization->isAllowed(
            'Designnbuy_Font::import_export'
        );

    }
}
