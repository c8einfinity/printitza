<?php

namespace Designnbuy\Template\Controller\Adminhtml\Template;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Export Csv action
 * @category Designnbuy
 * @package  Designnbuy_Template
 * @module   Template
 * @author   Designnbuy Developer
 */
class ExportCsv extends \Designnbuy\Template\Controller\Adminhtml\Template
{
    /**
     * Dispatch request
     */
    public function execute()
    {
        $fileName = 'owlcarousel_templates.csv';

        /** @var \\Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $content = $resultPage->getLayout()
            ->createBlock('Designnbuy\Template\Block\Adminhtml\Template\Grid')->getCsv();

        return $this->_fileFactory->create($fileName, $content, DirectoryList::VAR_DIR);
    }
}
