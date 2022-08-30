<?php

namespace Designnbuy\Designidea\Controller\Adminhtml\Category;

/**
 * Delete Designidea action
 * @category Designnbuy
 * @package  Designnbuy_Designidea
 * @module   Designidea
 * @author   Designnbuy Developer
 */
class Delete extends \Designnbuy\Designidea\Controller\Adminhtml\Category
{
    /**
     * Dispatch request
     */
    public function execute()
    {
        $categoryId = $this->getRequest()->getParam(static::PARAM_ID);
        try {
            $designidea = $this->_categoryFactory->create()->setId($categoryId);
            $designidea->delete();
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
