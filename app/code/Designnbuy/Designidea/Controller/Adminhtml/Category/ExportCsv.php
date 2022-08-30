<?php

namespace Designnbuy\Designidea\Controller\Adminhtml\Category;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Export Csv action
 * @category Designnbuy
 * @package  Designnbuy_Designidea
 * @module   Designidea
 * @author   Designnbuy Developer
 */
class ExportCsv extends \Designnbuy\Designidea\Controller\Adminhtml\Category
{
    /**
     * Dispatch request
     */
    public function execute()
    {
        $fileName = 'designideas_category.csv';

        /** @var \\Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $content = $resultPage->getLayout()
            ->createBlock('Designnbuy\Designidea\Block\Adminhtml\Category\Grid')->getCsv();

        return $this->_fileFactory->create($fileName, $content, DirectoryList::VAR_DIR);
    }
}
