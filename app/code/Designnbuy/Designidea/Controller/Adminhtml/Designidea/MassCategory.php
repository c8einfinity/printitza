<?php

namespace Designnbuy\Designidea\Controller\Adminhtml\Designidea;

/**
 * Mass Delete Template action
 * @category Designnbuy
 * @package  Designnbuy_Template
 * @module   Template
 * @author   Designnbuy Developer
 */
class MassCategory extends \Designnbuy\Designidea\Controller\Adminhtml\Designidea
{
    /**
     * Dispatch request
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $templateIds = $this->getRequest()->getParam('designidea');
        $categoryId = $this->getRequest()->getParam('category_id');

        if (!is_array($templateIds) || empty($templateIds)) {
            $this->messageManager->addError(__('Please select editable artwork(s).'));
        } else {
            /* $templateCollection = $this->_templateCollectionFactory->create()
                ->addAttributeToSelect('title')
                ->addFieldToFilter('entity_id', ['in' => $templateIds]); */
            try {

                foreach ($templateIds as $templateId) 
                {
                    $post = $this->_objectManager->get('Designnbuy\Designidea\Model\Designidea')->load($templateId);
                    $post->setStoreId(0)->setCategoryId([$categoryId])->save();
                }


                $this->messageManager->addSuccess(
                    __('A total of %1 editable artwork(s) have been updated.', count($templateIds))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        $resultRedirect = $this->resultRedirectFactory->create();

        return $resultRedirect->setPath('*/*/');
    }
}
