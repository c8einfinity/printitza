<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Customer\Controller\Adminhtml\Queue;

class Preview extends \Designnbuy\Customer\Controller\Adminhtml\Queue
{
    /**
     * Preview Customer queue template
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $data = $this->getRequest()->getParams();

        $isEmptyRequestData = empty($data) || !isset($data['id']);
        $isEmptyPreviewData = !$this->_getSession()->hasPreviewData() || empty($this->_getSession()->getPreviewData());
        
        if ($isEmptyRequestData && $isEmptyPreviewData) {
            $this->_forward('noroute');
            return;
        }

        // set default value for selected store
        /** @var \Magento\Store\Model\StoreManager $storeManager */
        $storeManager = $this->_objectManager->get('Magento\Store\Model\StoreManager');
        $defaultStore = $storeManager->getDefaultStoreView();
        if (!$defaultStore) {
            $allStores = $storeManager->getStores();
            if (isset($allStores[0])) {
                $defaultStore = $allStores[0];
            }
        }
        $data['preview_store_id'] = $defaultStore ? $defaultStore->getId() : null;

        $this->_view->getLayout()->getBlock('preview_form')->setFormData($data);
        $this->_view->renderLayout();
    }
}
