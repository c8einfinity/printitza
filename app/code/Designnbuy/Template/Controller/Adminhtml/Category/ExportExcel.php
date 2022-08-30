<?php

namespace Designnbuy\Template\Controller\Adminhtml\Category;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Export Excel action
 * @category Designnbuy
 * @package  Designnbuy_Template
 * @module   Template
 * @author   Designnbuy Developer
 */
class ExportExcel extends \Designnbuy\Template\Controller\Adminhtml\Template
{
    /**
     * Dispatch request
     */
    public function execute()
    {
        $fileName = 'templates_category.xls';

        /** @var \\Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $content = $resultPage->getLayout()
            ->createBlock('Designnbuy\Template\Block\Adminhtml\Category\Grid')->getExcel();

        return $this->_fileFactory->create($fileName, $content, DirectoryList::VAR_DIR);
    }
}
