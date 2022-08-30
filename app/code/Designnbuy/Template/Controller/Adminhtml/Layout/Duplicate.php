<?php

namespace Designnbuy\Template\Controller\Adminhtml\Layout;

/**
 * Edit Template action
 * @category Designnbuy
 * @package  Designnbuy_Template
 * @module   Template
 * @author   Designnbuy Developer
 */
class Duplicate extends \Designnbuy\Template\Controller\Adminhtml\Template
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Dispatch request
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $storeId = $this->getRequest()->getParam('store', 0);
        $store = $this->getStoreManager()->getStore($storeId);
        $this->getStoreManager()->setCurrentStore($store->getCode());

        $redirectBack = 'duplicate';
        $templateId = $this->getRequest()->getParam('id');
        $resultRedirect = $this->resultRedirectFactory->create();
        $formPostValues = $this->getRequest()->getPostValue();

        if (isset($templateId)) {
           
            $templateModel = $this->_templateFactory->create();

            $typeId = $this->getRequest()->getParam('type');

            if (!$templateId && $typeId) {
                $templateModel->setTypeId($typeId);
            }

            $templateModel->setStoreId($this->getRequest()->getParam('store', 0));
            $templateModel->load($templateId);

            try {
                $templateModel = $templateModel->duplicate();
                $templateId = $templateModel->getEntityId();
                $this->messageManager->addSuccess(__('The layout has been duplicated.'));
                $this->_getSession()->setFormData(false);
                //return $this->_getResultRedirect($resultRedirect, $templateModel->getId());
            } /*catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->messageManager->addException($e, __('Something went wrong while saving the template.'));
            }*/
            catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_getSession()->setFormData($formPostValues);
                $redirectBack = $templateId ? true : 'new';
            } catch (\Exception $e) {
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->messageManager->addError($e->getMessage());
                $this->_getSession()->setFormData($formPostValues);
                $redirectBack = $templateId ? true : 'new';
            }

            //return $resultRedirect->setPath('*/*/edit', ['id' => $templateId]);
        }else {
            $resultRedirect->setPath('*/*/', ['store' => $storeId]);
            $this->messageManager->addError('No data to save');
            return $resultRedirect;
        }

        if ($redirectBack === 'new') {
            $resultRedirect->setPath(
                '*/*/new'
            );
        } elseif ($redirectBack === 'duplicate') {
            $resultRedirect->setPath(
                '*/*/edit',
                ['id' => $templateModel->getEntityId(), 'back' => null, '_current' => true]
            );
        } elseif ($redirectBack) {
            $resultRedirect->setPath(
                '*/*/edit',
                ['id' => $templateId, '_current' => true]
            );
        } else {
            $resultRedirect->setPath('*/*/', ['store' => $storeId]);
        }
        return $resultRedirect;
    }

    /**
     * @return StoreManagerInterface
     * @deprecated
     */
    private function getStoreManager()
    {
        if (null === $this->storeManager) {
            $this->storeManager = \Magento\Framework\App\ObjectManager::getInstance()
                ->get('Magento\Store\Model\StoreManagerInterface');
        }
        return $this->storeManager;
    }
}
