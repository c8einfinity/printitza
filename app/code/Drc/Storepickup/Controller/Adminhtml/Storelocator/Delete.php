<?php
/**
 * {{Drc}}_{{Storepickup}} extension
 *                     NOTICE OF LICENSE
 *
 *                     This source file is subject to the MIT License
 *                     that is bundled with this package in the file LICENSE.txt.
 *                     It is also available through the world-wide-web at this URL:
 *                     http://opensource.org/licenses/mit-license.php
 *
 *                     @category  {{Drc}}
 *                     @package   {{Drc}}_{{Storepickup}}
 *                     @copyright Copyright (c) {{2016}}
 *                     @license   http://opensource.org/licenses/mit-license.php MIT License
 */
namespace Drc\Storepickup\Controller\Adminhtml\Storelocator;

class Delete extends \Drc\Storepickup\Controller\Adminhtml\Storelocator
{
    /**
     * execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('storelocator_id');
        if ($id) {
            $store_title = "";
            try {
                /** @var \Drc\Storepickup\Model\Storelocator $storelocator */
                $storelocator = $this->storelocatorFactory->create();
                $storelocator->load($id);
                $store_title = $storelocator->getStore_title();
                $storelocator->delete();
                $this->messageManager->addSuccess(__('The Storelocator has been deleted.'));
                $this->_eventManager->dispatch(
                    'adminhtml_drc_storepickup_storelocator_on_delete',
                    ['store_title' => $store_title, 'status' => 'success']
                );
                $resultRedirect->setPath('drc_storepickup/*/');
                return $resultRedirect;
            } catch (\Exception $e) {
                $this->_eventManager->dispatch(
                    'adminhtml_drc_storepickup_storelocator_on_delete',
                    ['store_title' => $store_title, 'status' => 'fail']
                );
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                $resultRedirect->setPath('drc_storepickup/*/edit', ['storelocator_id' => $id]);
                return $resultRedirect;
            }
        }
        // display error message
        $this->messageManager->addError(__('Storelocator to delete was not found.'));
        // go to grid
        $resultRedirect->setPath('drc_storepickup/*/');
        return $resultRedirect;
    }
}
