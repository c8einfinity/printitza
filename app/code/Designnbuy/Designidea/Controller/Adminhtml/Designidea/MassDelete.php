<?php

namespace Designnbuy\Designidea\Controller\Adminhtml\Designidea;

/**
 * Mass Delete Designidea action
 * @category Designnbuy
 * @package  Designnbuy_Designidea
 * @module   Designidea
 * @author   Designnbuy Developer
 */
class MassDelete extends \Designnbuy\Designidea\Controller\Adminhtml\Designidea
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
            $this->messageManager->addError(__('Please select editable artwork(s).'));
        } else {
            $designideaCollection = $this->_designideaCollectionFactory->create()
                ->addFieldToFilter('entity_id', ['in' => $designideaIds]);
            try {
                foreach ($designideaCollection as $designidea) {
                    $designidea->delete();
                }
                $this->messageManager->addSuccess(
                    __('A total of %1 editable artwork(s) have been deleted.', count($designideaIds))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        $resultRedirect = $this->resultRedirectFactory->create();

        return $resultRedirect->setPath('*/*/');
    }
}
