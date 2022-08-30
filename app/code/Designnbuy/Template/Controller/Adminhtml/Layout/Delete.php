<?php

namespace Designnbuy\Template\Controller\Adminhtml\Layout;

/**
 * Delete Template action
 * @category Designnbuy
 * @package  Designnbuy_Template
 * @module   Template
 * @author   Designnbuy Developer
 */
class Delete extends \Designnbuy\Template\Controller\Adminhtml\Template
{
    /**
     * Dispatch request
     */
    public function execute()
    {
        $templateId = $this->getRequest()->getParam(static::PARAM_ID);
        try {
            $template = $this->_templateFactory->create()->setId($templateId);
            $template->delete();
            $this->messageManager->addSuccess(
                __('Delete successfully !')
            );
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        $resultRedirect = $this->resultRedirectFactory->create();

        return $resultRedirect->setPath('*/*/');
    }
}
