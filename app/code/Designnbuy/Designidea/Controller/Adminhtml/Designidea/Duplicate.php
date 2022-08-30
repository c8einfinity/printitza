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
class Duplicate extends \Designnbuy\Designidea\Controller\Adminhtml\Designidea
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

        $designideaModel = $this->_designideaFactory->create();
        $designideaModel->load($designIdeaId);
        if ($designideaModel->getEntityId()) {

            try {
                $object = clone $designideaModel;
                $object
                    ->unsetData('entity_id')
                    ->setTitle($object->getTitle() . ' (' . __('Duplicated') . ')')
                    ->setWebsiteIds([1 => 1]);

                $object->save();

                $designIdeaId = $designideaModel->getEntityId();
                $this->messageManager->addSuccess(__('The editable artwork has been duplicated.'));
                $this->_getSession()->setFormData(false);
                //return $this->_getResultRedirect($resultRedirect, $designideaModel->getId());
            } /*catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->messageManager->addException($e, __('Something went wrong while saving the designidea.'));
            }*/
            catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                $redirectBack = $designIdeaId ? true : 'new';
            } catch (\Exception $e) {
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->messageManager->addError($e->getMessage());
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
