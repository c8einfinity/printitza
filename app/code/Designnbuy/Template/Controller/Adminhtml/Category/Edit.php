<?php

namespace Designnbuy\Template\Controller\Adminhtml\Category;

/**
 * Edit Template action
 * @category Designnbuy
 * @package  Designnbuy_Template
 * @module   Template
 * @author   Designnbuy Developer
 */
class Edit extends \Designnbuy\Template\Controller\Adminhtml\Category
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
        $category = $this->_objectManager->create('Designnbuy\Template\Model\Category');
        
        $category->setStoreId($storeId);
        $category->setAttributeSetId($category->getDefaultAttributeSetId());
        //$template = $this->_templateFactory->create();
        //$template->setStoreId($this->getRequest()->getParam('store', 0));
        if ($id) {
            $category->load($id);
            if (!$category->getId()) {
                $this->messageManager->addError(__('This category no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }

        $data = $this->_getSession()->getFormData(true);

        if (!empty($data)) {
            //$category->setData($data);
        }
        
        $this->_coreRegistry->register('current_category', $category);
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
