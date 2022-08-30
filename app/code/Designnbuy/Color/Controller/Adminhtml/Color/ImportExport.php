<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Color\Controller\Adminhtml\Color;

use Magento\Framework\Controller\ResultFactory;

class ImportExport extends \Designnbuy\Color\Controller\Adminhtml\Color
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Designnbuy_Color::import_export';

    /**
     * Import and export Page
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $resultPage->setActiveMenu('Designnbuy_Color::import_export');
        $resultPage->addContent(
            $resultPage->getLayout()->createBlock('Designnbuy\Color\Block\Adminhtml\Color\ImportExportHeader')
        );
        $resultPage->addContent(
            $resultPage->getLayout()->createBlock('Designnbuy\Color\Block\Adminhtml\Color\ImportExport')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Color'));
        $resultPage->getConfig()->getTitle()->prepend(__('Import and Export Color'));
        return $resultPage;
    }
}
