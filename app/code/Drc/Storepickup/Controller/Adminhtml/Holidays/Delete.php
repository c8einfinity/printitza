<?php
/**
 * Copyright © 2019 Designnbuy WebToPrint Pvt. Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 * ALWAYS DO BETTER @a
 */

namespace Drc\Storepickup\Controller\Adminhtml\Holidays;

class Delete extends \Drc\Storepickup\Controller\Adminhtml\Holidays
{
    /**
     * execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('entity_id');
        if ($id) {
            $title = "";
            try {
                /** @var \Drc\Storepickup\Model\Storelocator $storelocator */
                $storelocator = $this->holidaysFactory->create();
                $storelocator->load($id);
                $title = $storelocator->getTitle();
                $storelocator->delete();
                $this->messageManager->addSuccess(__('The Holidays has been deleted.'));
                $this->_eventManager->dispatch(
                    'adminhtml_drc_storepickup_holidays_on_delete',
                    ['title' => $title, 'status' => 'success']
                );
                $resultRedirect->setPath('drc_storepickup/*/');
                return $resultRedirect;
            } catch (\Exception $e) {
                $this->_eventManager->dispatch(
                    'adminhtml_drc_storepickup_holidays_on_delete',
                    ['title' => $title, 'status' => 'fail']
                );
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                $resultRedirect->setPath('drc_storepickup/*/edit', ['entity_id' => $id]);
                return $resultRedirect;
            }
        }
        // display error message
        $this->messageManager->addError(__('Holiday to delete was not found.'));
        // go to grid
        $resultRedirect->setPath('drc_storepickup/*/');
        return $resultRedirect;
    }
}
