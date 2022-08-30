<?php

namespace Designnbuy\Designidea\Controller\Adminhtml\Designidea;

/**
 * Edit Designidea action
 * @category Designnbuy
 * @package  Designnbuy_Designidea
 * @module   Designidea
 * @author   Designnbuy Developer
 */
class Edit extends \Designnbuy\Designidea\Controller\Adminhtml\Designidea
{
    /**
     * @var StoreFactory
     */
    protected $storeFactory;
    /**
     * Dispatch request
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        $storeId = (int)$this->getRequest()->getParam('store');

        $store = $this->getStoreFactory()->create();
        $store->load($this->getRequest()->getParam('store', 0));

        $id = $this->getRequest()->getParam('id');
        //$designidea = $this->_objectManager->create('Designnbuy\Designidea\Model\Designidea');
        $designidea = $this->_designideaFactory->create();
        $type = $this->getRequest()->getParam('type');

        if (!$id && $type) {
            $designidea->setTypeId($type);
        }

        $designidea->setStoreId($storeId);
        $designidea->setAttributeSetId($designidea->getDefaultAttributeSetId());

        if ($id) {
            $designidea->load($id);
            if (!$designidea->getId()) {
                $this->messageManager->addError(__('This editable artwork no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

//        $title = 'Designidea';
        /*if ($designidea->getId()) {
            $breadcrumbTitle = __('Edit %1', $title);
            $breadcrumbLabel = $breadcrumbTitle;
        } else {
            $breadcrumbTitle = __('New %1', $title);
            $breadcrumbLabel = __('Create %1', $title);
        }*/


        /*$resultPage->getConfig()->getTitle()->prepend(__($title));
        $resultPage->getConfig()->getTitle()->prepend($designidea->getTitle());*/

        
        $data = $this->_getSession()->getFormData(true);

        if (!empty($data)) {
            $designidea->setData($data);
        }

        $this->_coreRegistry->register('current_designidea', $designidea);
        $this->_coreRegistry->register('current_store', $store);
        return $resultPage;
    }

    /**
     * @return StoreFactory
     */
    private function getStoreFactory()
    {
        if (null === $this->storeFactory) {
            $this->storeFactory = \Magento\Framework\App\ObjectManager::getInstance()
                ->get('Magento\Store\Model\StoreFactory');
        }
        return $this->storeFactory;
    }
}
