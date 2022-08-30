<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\PrintingMethod\Controller\Adminhtml\PrintingMethod;

use Magento\Framework\Controller\ResultFactory;

class ImportExport extends \Designnbuy\PrintingMethod\Controller\Adminhtml\PrintingMethod
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Designnbuy_PrintingMethod::import_export';

    /**
     * Import and export Page
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $resultPage->setActiveMenu('Designnbuy_PrintingMethod::import_export');
        $resultPage->addContent(
            $resultPage->getLayout()->createBlock('Designnbuy\PrintingMethod\Block\Adminhtml\PrintingMethod\ImportExportHeader')
        );
        $resultPage->addContent(
            $resultPage->getLayout()->createBlock('Designnbuy\PrintingMethod\Block\Adminhtml\PrintingMethod\ImportExport')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('PrintingMethod'));
        $resultPage->getConfig()->getTitle()->prepend(__('Import and Export PrintingMethod'));
        return $resultPage;
    }
}
