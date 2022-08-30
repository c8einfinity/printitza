<?php

namespace Designnbuy\Template\Controller\Adminhtml\Category;

/**
 * Mass Status action
 * @category Designnbuy
 * @package  Designnbuy_Template
 * @module   Template
 * @author   Designnbuy Developer
 */
class MassStatus extends \Designnbuy\Template\Controller\Adminhtml\Category
{
    /**
     * Dispatch request
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $categoryIds = $this->getRequest()->getParam('category');
        $status = $this->getRequest()->getParam('status');
        $storeId = $this->getRequest()->getParam('store',0);

        if (!is_array($categoryIds) || empty($categoryIds)) {
            $this->messageManager->addError(__('Please select category(s).'));
        } else {
            try {
                $this->_objectManager->get(\Designnbuy\Template\Model\Category\Action::class)
                    ->updateAttributes($categoryIds, ['status' => $status], $storeId);
                /*$categoryCollection = $this->_categoryCollectionFactory->create()
                    ->addFieldToFilter('entity_id', ['in' => $categoryIds]);

                foreach ($categoryCollection as $category) {
                    if($storeId){
                        $category->setStoreId($store);
                    }
                    $category->setStatus($status);
                    $category->setIsMassupdate(true);
                    $category->save();
                }*/
                $this->messageManager->addSuccess(
                    __('A total of %1 category(s) status have been changed.', count($categoryIds))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        $resultRedirect = $this->resultRedirectFactory->create();

        return $resultRedirect->setPath('*/*/');
    }
}
