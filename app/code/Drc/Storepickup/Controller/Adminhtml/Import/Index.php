<?php
/**
 * @author Drc Systems India Pvt Ltd.
*/

namespace Drc\Storepickup\Controller\Adminhtml\Import;

class Index extends \Drc\Storepickup\Controller\Adminhtml\Import
{

    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Drc_Storepickup::import');
        $resultPage->getConfig()->getTitle()->prepend(__('Import'));
        $resultPage->addBreadcrumb(__('Drc'), __('Drc'));
        $resultPage->addBreadcrumb(__('Import'), __('Import'));
        return $resultPage;
    }
}
