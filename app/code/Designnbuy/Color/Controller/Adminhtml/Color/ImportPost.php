<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Color\Controller\Adminhtml\Color;

use Magento\Framework\Controller\ResultFactory;

class ImportPost extends \Designnbuy\Color\Controller\Adminhtml\Color
{
    /**
     * import action from import/export color
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        if ($this->getRequest()->isPost() && !empty($_FILES['import_color_file']['tmp_name'])) {
            try {
                /** @var $importHandler \Designnbuy\Color\Model\Color\CsvImportHandler */
                $importHandler = $this->_objectManager->create('Designnbuy\Color\Model\Color\CsvImportHandler');
                $importHandler->importFromCsvFile($this->getRequest()->getFiles('import_color_file'));

                $this->messageManager->addSuccess(__('The color has been imported.'));
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
            'Designnbuy_Color::elements'
        ) || $this->_authorization->isAllowed(
            'Designnbuy_Color::import_export'
        );

    }
}
