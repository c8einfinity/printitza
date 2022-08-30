<?php

namespace Designnbuy\Template\Controller\Adminhtml\Layout;

/**
 * Mass Status action
 * @category Designnbuy
 * @package  Designnbuy_Template
 * @module   Template
 * @author   Designnbuy Developer
 */
class MassStatus extends \Designnbuy\Template\Controller\Adminhtml\Template
{
    /**
     * Dispatch request
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $templateIds = $this->getRequest()->getParam('template');
        $status = $this->getRequest()->getParam('status');
        
        if (!is_array($templateIds) || empty($templateIds)) {
            $this->messageManager->addError(__('Please select template(s).'));
        } else {
            try {
                $templateCollection = $this->_templateCollectionFactory->create()
                    ->addFieldToFilter('entity_id', ['in' => $templateIds]);

                foreach ($templateCollection as $template) {
                    $template->setStatus($status)
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->messageManager->addSuccess(
                    __('A total of %1 template(s) status have been changed.', count($templateIds))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        $resultRedirect = $this->resultRedirectFactory->create();

        return $resultRedirect->setPath('*/*/');
    }
}
