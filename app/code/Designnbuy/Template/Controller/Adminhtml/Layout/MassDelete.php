<?php

namespace Designnbuy\Template\Controller\Adminhtml\Layout;

/**
 * Mass Delete Template action
 * @category Designnbuy
 * @package  Designnbuy_Template
 * @module   Template
 * @author   Designnbuy Developer
 */
class MassDelete extends \Designnbuy\Template\Controller\Adminhtml\Template
{
    /**
     * Dispatch request
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $templateIds = $this->getRequest()->getParam('template');
        
        if (!is_array($templateIds) || empty($templateIds)) {
            $this->messageManager->addError(__('Please select template(s).'));
        } else {
            $templateCollection = $this->_templateCollectionFactory->create()
                ->addFieldToFilter('entity_id', ['in' => $templateIds]);
            try {
                foreach ($templateCollection as $template) {
                    $template->delete();
                }
                $this->messageManager->addSuccess(
                    __('A total of %1 template(s) have been deleted.', count($templateIds))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        $resultRedirect = $this->resultRedirectFactory->create();

        return $resultRedirect->setPath('*/*/');
    }
}
