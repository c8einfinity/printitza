<?php

namespace Designnbuy\Designidea\Controller\Adminhtml\Designidea;

class MassIdentifier extends \Designnbuy\Designidea\Controller\Adminhtml\Designidea
{
    /**
     * Dispatch request
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $designideaIds = $this->getRequest()->getParam('designidea');
        
        if (!is_array($designideaIds) || empty($designideaIds)) {
            $this->messageManager->addError(__('Please select template(s).'));
        } else {
            $designideaCollection = $this->_designideaCollectionFactory->create()
                ->addAttributeToSelect('title')
                ->addFieldToFilter('entity_id', ['in' => $designideaIds]);
            try {

                foreach ($designideaCollection as $designidea) 
                {
                    $title = strtolower($designidea->getTitle());
                    $titleIdentifier = str_replace(" ", "-", $title);

                    /*$product = $designidea->getRelatedProductCollection()->addAttributeToFilter('attribute_set_id', 9)->getFirstItem();
                    if($product->getId() != ''){
                        $designidea
                            ->setStoreId(0)
                            ->setProductId($product->getId())
                            ->setIsMassupdate(true)
                            ->save();
                    }*/

                    $designidea
                        ->setStoreId(0)
                        ->setIdentifier($title)
                        ->setIsMassupdate(true)
                        ->save();
                }

                $this->messageManager->addSuccess(
                    __('A total of %1 editable artwork(s) have been updated.', count($designideaIds))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        $resultRedirect = $this->resultRedirectFactory->create();

        return $resultRedirect->setPath('*/*/');
    }
}
