<?php

namespace Designnbuy\Template\Controller\Adminhtml\Template;

/**
 * Edit Template action
 * @category Designnbuy
 * @package  Designnbuy_Template
 * @module   Template
 * @author   Designnbuy Developer
 */
class Edit extends \Designnbuy\Template\Controller\Adminhtml\Template
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
        //$template = $this->_objectManager->create('Designnbuy\Template\Model\Template');
        $template = $this->_templateFactory->create();
        $type = $this->getRequest()->getParam('type');
        $type = 'template';
        if (!$id && $type) {
            $template->setTypeId($type);
        }

        $template->setStoreId($storeId);
        $template->setAttributeSetId($template->getDefaultAttributeSetId());

        if ($id) {
            $template->load($id);
            /*if ($template->getId()) {
                $title = $template->getTitle();
                $breadcrumbTitle = __('Edit %1', $title);
                $breadcrumbLabel = $breadcrumbTitle;
            } else {
                $title = 'Template';
                $breadcrumbTitle = __('New %1', $title);
                $breadcrumbLabel = __('Create %1', $title);
            }
            $this->_view->getPage()->getConfig()->getTitle()->prepend(__($title));
            $this->_view->getPage()->getConfig()->getTitle()->prepend(
                $template->getId() ? 'Template' : __('New %1', $title)
            );

            $this->_addBreadcrumb($breadcrumbLabel, $breadcrumbTitle);
            */
            if (!$template->getId()) {
                $this->messageManager->addError(__('This template no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }
        
        $data = $this->_getSession()->getFormData(true);

        if (!empty($data)) {
            $template->setData($data);
        }

        $this->_coreRegistry->register('current_template', $template);
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
