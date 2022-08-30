<?php

namespace Designnbuy\Template\Controller\Adminhtml\Layout;

use Designnbuy\Template\Model\Template;

/**
 * Save Template action
 * @category Designnbuy
 * @package  Designnbuy_Template
 * @module   Template
 * @author   Designnbuy Developer
 */
class Save extends \Designnbuy\Template\Controller\Adminhtml\Template
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
        $redirectBack = $this->getRequest()->getParam('back', false);
        $templateId = $productId = $this->getRequest()->getParam('id');
        $resultRedirect = $this->resultRedirectFactory->create();
        $formPostValues = $this->getRequest()->getPostValue();
       
        if (isset($formPostValues)) {
            $templateData = $formPostValues['template'];
            //unset($templateData['image']);
            $templateModel = $this->_templateFactory->create();
            $templateModel->setStoreId($this->getRequest()->getParam('store', 0));
            $templateModel->load($templateId);
            $templateModel->addData($templateData);

            $templateModel->setAttributeSetId($templateModel->getDefaultAttributeSetId());
            $templateModel->setEntityTypeId(10);
            
            $typeId = $this->getRequest()->getParam('type');
            
            if (!$templateId && $typeId) {
                $templateModel->setTypeId($typeId);
            }
            
            try {
                /**
                 * Check "Use Default Value" checkboxes values
                 */
                if (isset($formPostValues['use_default']) && !empty($formPostValues['use_default'])) {
                    foreach ($formPostValues['use_default'] as $attributeCode => $attributeValue) {
                        if ($attributeValue) {
                            $templateModel->setData($attributeCode, null);
                        }
                    }
                }
                $data = $this->getRequest()->getPost();
                
                $links = isset($data['links']) ? $data['links'] : ['product' => []];
                /* Prepare relative links */

                if ($links && is_array($links)) {
                    foreach (['product'] as $linkType) {
                        if (!empty($links[$linkType]) && is_array($links[$linkType])) {
                            $linksData = [];
                            foreach ($links[$linkType] as $item) {
                                $linksData[$item['id']] = [
                                    'position' => $item['position']
                                ];
                            }
                            $links[$linkType] = $linksData;
                        } else {
                            $links[$linkType] = [];
                        }
                    }
                    $templateModel->setProductsData($links);
                }


                $templateModel->save();
                $templateId = $templateModel->getEntityId();
                $this->messageManager->addSuccess(__('The layout has been saved.'));
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
            $resultRedirect->setPath('*/layout/', ['store' => $storeId]);
            $this->messageManager->addError('No data to save');
            return $resultRedirect;
        }

        if ($redirectBack === 'new') {
            $resultRedirect->setPath(
                '*/layout/new'
            );
        } elseif ($redirectBack === 'duplicate' && isset($newProduct)) {
            $resultRedirect->setPath(
                '*/layout/edit',
                ['id' => $templateModel->getEntityId(), 'back' => null, '_current' => true]
            );
        } elseif ($redirectBack) {
            $resultRedirect->setPath(
                '*/layout/edit',
                ['id' => $templateId, '_current' => true]
            );
        } else {
            $resultRedirect->setPath('*/layout/', ['store' => $storeId]);
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
