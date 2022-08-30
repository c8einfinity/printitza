<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Font\Controller\Adminhtml\Font;

use Magento\Framework\Controller\ResultFactory;

class ImportExport extends \Designnbuy\Font\Controller\Adminhtml\Font
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Designnbuy_Font::import_export';

    /**
     * Import and export Page
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $resultPage->setActiveMenu('Designnbuy_Font::import_export');
        $resultPage->addContent(
            $resultPage->getLayout()->createBlock('Designnbuy\Font\Block\Adminhtml\Font\ImportExportHeader')
        );
        $resultPage->addContent(
            $resultPage->getLayout()->createBlock('Designnbuy\Font\Block\Adminhtml\Font\ImportExport')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Font'));
        $resultPage->getConfig()->getTitle()->prepend(__('Import and Export Font'));
        return $resultPage;
    }
}
