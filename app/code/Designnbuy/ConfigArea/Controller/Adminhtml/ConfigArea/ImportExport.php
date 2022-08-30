<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\ConfigArea\Controller\Adminhtml\ConfigArea;

use Magento\Framework\Controller\ResultFactory;

class ImportExport extends \Designnbuy\ConfigArea\Controller\Adminhtml\ConfigArea
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Designnbuy_ConfigArea::import_export';

    /**
     * Import and export Page
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $resultPage->setActiveMenu('Designnbuy_ConfigArea::import_export');
        $resultPage->addContent(
            $resultPage->getLayout()->createBlock('Designnbuy\ConfigArea\Block\Adminhtml\ConfigArea\ImportExportHeader')
        );
        $resultPage->addContent(
            $resultPage->getLayout()->createBlock('Designnbuy\ConfigArea\Block\Adminhtml\ConfigArea\ImportExport')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Design Area'));
        $resultPage->getConfig()->getTitle()->prepend(__('Import and Export Design Area'));
        return $resultPage;
    }
}
