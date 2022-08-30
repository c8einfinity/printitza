<?php

namespace Designnbuy\Designidea\Controller\Adminhtml\Designidea;

use Designnbuy\Designidea\Model\Designidea;

/**
 * Save Designidea action
 * @category Designnbuy
 * @package  Designnbuy_Designidea
 * @module   Designidea
 * @author   Designnbuy Developer
 */
class Save extends \Designnbuy\Designidea\Controller\Adminhtml\Designidea
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
        $designIdeaId = $this->getRequest()->getParam('id');
        $resultRedirect = $this->resultRedirectFactory->create();
        $formPostValues = $this->getRequest()->getPostValue();
       
        if (isset($formPostValues)) {
            $designideaData = $formPostValues['designidea'];
            //unset($designideaData['image']);
            $designideaModel = $this->_designideaFactory->create();

            $typeId = $this->getRequest()->getParam('type');

            if (!$designIdeaId && $typeId) {
                $designideaModel->setTypeId($typeId);
            }

            $designideaModel->setStoreId($this->getRequest()->getParam('store', 0));
            $designideaModel->load($designIdeaId);
            $designideaModel->addData($designideaData);

            $designideaModel->setAttributeSetId($designideaModel->getDefaultAttributeSetId());
            $designideaModel->setEntityTypeId(16);
            try {
                /**
                 * Check "Use Default Value" checkboxes values
                 */
                if (isset($formPostValues['use_default']) && !empty($formPostValues['use_default'])) {
                    foreach ($formPostValues['use_default'] as $attributeCode => $attributeValue) {
                        if ($attributeValue) {
                            $designideaModel->setData($attributeCode, null);
                        }
                    }
                }
               
                /* Prepare relative links */
                /*if (isset($formPostValues['links'])) {
                    $links = isset($formPostValues['links']) ? $formPostValues['links'] : null;
                    if ($links && is_array($links)) {
                        foreach (['product'] as $linkType) {
                            if (!empty($links[$linkType]) && is_array($links[$linkType])) {
                                $linksData = [];
                                foreach ($links[$linkType] as $item) {
                                    $linksData[$item['id']] = [
                                        'position' => $item['position']
                                    ];
                                }
                                //$links[$linkType] = $linksData;
                            }
                        }
                        $designideaModel->setProductsData($linksData);
                    }
                }*/
                $data = $this->getRequest()->getPost();
                $upsellLinks = isset($data['links']) ? $data['links'] : ['upsell' => []];
                if ($upsellLinks && is_array($upsellLinks)) {
                    foreach (['upsell'] as $linkType) {
                        if (!empty($upsellLinks[$linkType]) && is_array($upsellLinks[$linkType])) {
                            $linksData = [];
                            foreach ($upsellLinks[$linkType] as $item) {
                                $upsellLinksData[$item['id']] = [
                                    'position' => $item['position']
                                ];
                            }
                            $upsellLinks[$linkType] = $upsellLinksData;
                        } else {
                            $upsellLinks[$linkType] = [];
                        }
                    }
                    $designideaModel->setUpsellData($upsellLinks);
                }

                $crosssellLinks = isset($data['links']) ? $data['links'] : ['crosssell' => []];
                if ($crosssellLinks && is_array($crosssellLinks)) {
                    foreach (['crosssell'] as $linkType) {
                        if (!empty($crosssellLinks[$linkType]) && is_array($crosssellLinks[$linkType])) {
                            $crosssellLinksData = [];
                            foreach ($crosssellLinks[$linkType] as $item) {
                                $crosssellLinksData[$item['id']] = [
                                    'position' => $item['position']
                                ];
                            }
                            $crosssellLinks[$linkType] = $crosssellLinksData;
                        } else {
                            $crosssellLinks[$linkType] = [];
                        }
                    }
                    $designideaModel->setCrosssellData($crosssellLinks);
                }
                $designideaModel->save();

                $designIdeaId = $designideaModel->getEntityId();
                $this->messageManager->addSuccess(__('The editable artwork has been saved.'));
                $this->_getSession()->setFormData(false);
                //return $this->_getResultRedirect($resultRedirect, $designideaModel->getId());
            } /*catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->messageManager->addException($e, __('Something went wrong while saving the designidea.'));
            }*/
            catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_getSession()->setFormData($formPostValues);
                $redirectBack = $designIdeaId ? true : 'new';
            } catch (\Exception $e) {
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->messageManager->addError($e->getMessage());
                $this->_getSession()->setFormData($formPostValues);
                $redirectBack = $designIdeaId ? true : 'new';
            }

            //return $resultRedirect->setPath('*/*/edit', ['id' => $designideaId]);
        }else {
            $resultRedirect->setPath('*/*/', ['store' => $storeId]);
            $this->messageManager->addError('No data to save');
            return $resultRedirect;
        }

        if ($redirectBack === 'new') {
            $resultRedirect->setPath(
                '*/*/new'
            );
        } elseif ($redirectBack === 'duplicate' && isset($newProduct)) {
            $resultRedirect->setPath(
                '*/*/edit',
                ['id' => $designideaModel->getEntityId(), 'back' => null, '_current' => true]
            );
        } elseif ($redirectBack) {
            $resultRedirect->setPath(
                '*/*/edit',
                ['id' => $designIdeaId, '_current' => true]
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
