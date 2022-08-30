<?php

namespace Designnbuy\Template\Controller\Adminhtml\Template;

/**
 * Mass Delete Template action
 * @category Designnbuy
 * @package  Designnbuy_Template
 * @module   Template
 * @author   Designnbuy Developer
 */
class MassIdentifier extends \Designnbuy\Template\Controller\Adminhtml\Template
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
                ->addAttributeToSelect('title')
                ->addFieldToFilter('entity_id', ['in' => $templateIds]);
            try {

                foreach ($templateCollection as $template) 
                {
                    $title = strtolower($template->getTitle());
                    $titleIdentifier = str_replace(" ", "-", $title);

                    $product = $template->getRelatedProductCollection()->addAttributeToFilter('attribute_set_id', 9)->getFirstItem();
                    if($product->getId() != ''){
                        $template
                            ->setStoreId(0)
                            ->setProductId($product->getId())
                            ->setIsMassupdate(true)
                            ->save();
                    }
                    /*foreach ($products as $product) {

                    }*/
                    /*$template
                        ->setStoreId(0)
                        ->setIdentifier($titleIdentifier)
                        ->setIsMassupdate(true)
                        ->save();*/
                    //$template->setStoreId(0);
                    //$template->setIdentifier($titleIdentifier);
                    //$template->save();
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
