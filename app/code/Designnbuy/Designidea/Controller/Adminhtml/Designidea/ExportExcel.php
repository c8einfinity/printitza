<?php

namespace Designnbuy\Designidea\Controller\Adminhtml\Designidea;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Export Excel action
 * @category Designnbuy
 * @package  Designnbuy_Designidea
 * @module   Designidea
 * @author   Designnbuy Developer
 */
class ExportExcel extends \Designnbuy\Designidea\Controller\Adminhtml\Designidea
{
    /**
     * Dispatch request
     */
    public function execute()
    {
        $fileName = 'owlcarousel_designideas.xls';

        /** @var \\Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $content = $resultPage->getLayout()
            ->createBlock('Designnbuy\Designidea\Block\Adminhtml\Designidea\Grid')->getExcel();

        return $this->_fileFactory->create($fileName, $content, DirectoryList::VAR_DIR);
    }
}
