<?php

namespace Designnbuy\Template\Controller\Adminhtml\Template;

/**
 * Mass Delete Template action
 * @category Designnbuy
 * @package  Designnbuy_Template
 * @module   Template
 * @author   Designnbuy Developer
 */
class MassCategory extends \Designnbuy\Template\Controller\Adminhtml\Template
{
    /**
     * Dispatch request
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $templateIds = $this->getRequest()->getParam('template');
        $categoryId = $this->getRequest()->getParam('category_id');

        if (!is_array($templateIds) || empty($templateIds)) {
            $this->messageManager->addError(__('Please select template(s).'));
        } else {
            /* $templateCollection = $this->_templateCollectionFactory->create()
                ->addAttributeToSelect('title')
                ->addFieldToFilter('entity_id', ['in' => $templateIds]); */
            try {

                foreach ($templateIds as $templateId) 
                {
                    $post = $this->_objectManager->get('Designnbuy\Template\Model\Template')->load($templateId);
                    $post->setStoreId(0)->setCategoryId([$categoryId])->save();
                }


                $this->messageManager->addSuccess(
                    __('A total of %1 template(s) have been updated.', count($templateIds))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        $resultRedirect = $this->resultRedirectFactory->create();

        return $resultRedirect->setPath('*/*/');
    }
}
