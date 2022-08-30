<?php

namespace Designnbuy\Color\Controller\Adminhtml\Color;

/**
 * Mass Delete Template action
 * @category Designnbuy
 * @package  Designnbuy_Template
 * @module   Template
 * @author   Designnbuy Developer
 */
class MassCategory extends \Designnbuy\Color\Controller\Adminhtml\Color
{
    /**
     * Dispatch request
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        //echo"<pre>"; print_r($this->getRequest()->getParams()); exit;
        $Ids = $this->getRequest()->getParam('id');
        $categoryId = $this->getRequest()->getParam('selected_category');

        if (!is_array($Ids) || empty($Ids)) {
            $this->messageManager->addError(__('Please select color(s).'));
        } else {
            /* $templateCollection = $this->_templateCollectionFactory->create()
                ->addAttributeToSelect('title')
                ->addFieldToFilter('entity_id', ['in' => $templateIds]); */
            try {

                foreach ($Ids as $templateId) 
                {
                    $post = $this->_objectManager->get('Designnbuy\Color\Model\Color')->load($templateId);
                    /* $catid = "";
                    if($post->getCategories()){
                        $catid = array_merge($post->getCategories(),[$categoryId]);
                    }
                    echo"<pre>"; print_r($catid); exit; */
                    $post->setStoreId(0)->setCategories([$categoryId])->save();
                }


                $this->messageManager->addSuccess(
                    __('A total of %1 color(s) have been updated.', count($Ids))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        $resultRedirect = $this->resultRedirectFactory->create();

        return $resultRedirect->setPath('*/*/');
    }
}
