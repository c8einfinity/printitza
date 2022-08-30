<?php

namespace Designnbuy\Template\Controller\Adminhtml\Category;

/**
 * Delete Template action
 * @category Designnbuy
 * @package  Designnbuy_Template
 * @module   Template
 * @author   Designnbuy Developer
 */
class Delete extends \Designnbuy\Template\Controller\Adminhtml\Category
{
    /**
     * Dispatch request
     */
    public function execute()
    {
        $categoryId = $this->getRequest()->getParam(static::PARAM_ID);
        try {
            $template = $this->_categoryFactory->create()->setId($categoryId);
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
