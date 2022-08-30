<?php

namespace Designnbuy\Designidea\Controller\Adminhtml\Category;

class MassIdentifier extends \Designnbuy\Designidea\Controller\Adminhtml\Category
{
    /**
     * Dispatch request
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $designideaIds = $this->getRequest()->getParam('category');

        if (!is_array($designideaIds) || empty($designideaIds)) {
            $this->messageManager->addError(__('Please select template(s).'));
        } else {
            $designideaCollection = $this->_categoryCollectionFactory->create()
                ->addAttributeToSelect('title')
                ->addFieldToFilter('entity_id', ['in' => $designideaIds]);
            try {

                foreach ($designideaCollection as $designidea) 
                {
                    $title = strtolower($designidea->getTitle());
                    $titleIdentifier = str_replace(" ", "-", $title);

                    $designidea
                        ->setStoreId(0)
                        ->setIdentifier($designidea->getTitle())
                        ->setIsMassupdate(true)
                        ->save();
                }

                $this->messageManager->addSuccess(
                    __('A total of %1 category(s) have been updated.', count($designideaIds))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        $resultRedirect = $this->resultRedirectFactory->create();

        return $resultRedirect->setPath('*/*/');
    }
}
