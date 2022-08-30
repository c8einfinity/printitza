<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Clipart\Controller\Adminhtml\Clipart;

use Magento\Framework\Controller\ResultFactory;

class ImportExport extends \Designnbuy\Clipart\Controller\Adminhtml\Clipart
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Designnbuy_Clipart::import_export';

    /**
     * Import and export Page
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $resultPage->setActiveMenu('Designnbuy_Clipart::import_export');
        $resultPage->addContent(
            $resultPage->getLayout()->createBlock('Designnbuy\Clipart\Block\Adminhtml\Clipart\ImportExportHeader')
        );
        $resultPage->addContent(
            $resultPage->getLayout()->createBlock('Designnbuy\Clipart\Block\Adminhtml\Clipart\ImportExport')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Clipart'));
        $resultPage->getConfig()->getTitle()->prepend(__('Import and Export Clipart'));
        return $resultPage;
    }
}
