<?php

namespace Designnbuy\Designidea\Controller\Adminhtml\Designidea;

/**
 * Mass Status action
 * @category Designnbuy
 * @package  Designnbuy_Designidea
 * @module   Designidea
 * @author   Designnbuy Developer
 */
class MassStatus extends \Designnbuy\Designidea\Controller\Adminhtml\Designidea
{
    /**
     * Dispatch request
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $designideaIds = $this->getRequest()->getParam('designidea');
        $status = $this->getRequest()->getParam('status');
        $storeId = $this->getRequest()->getParam('store',0);
        if (!is_array($designideaIds) || empty($designideaIds)) {
            $this->messageManager->addError(__('Please select editable artwork(s).'));
        } else {
            try {
                $this->_objectManager->get(\Designnbuy\Designidea\Model\Designidea\Action::class)
                    ->updateAttributes($designideaIds, ['status' => $status], $storeId);
                /*$designideaCollection = $this->_designideaCollectionFactory->create()
                    ->addFieldToFilter('entity_id', ['in' => $designideaIds]);

                foreach ($designideaCollection as $designidea) {
                    $designidea->setStatus($status)
                        ->setIsMassupdate(true)
                        ->save();
                }*/
                $this->messageManager->addSuccess(
                    __('A total of %1 editable artwork(s) status have been changed.', count($designideaIds))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        $resultRedirect = $this->resultRedirectFactory->create();

        return $resultRedirect->setPath('*/*/');
    }
}
