<?php
/**
 * Designnbuy_Reseller extension
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category  Designnbuy
 * @package   Designnbuy_Reseller
 * @copyright Copyright (c) 2018
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 */
namespace Designnbuy\Reseller\Controller\Adminhtml\Request;

class Delete extends \Designnbuy\Reseller\Controller\Adminhtml\Request
{
    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('request_id');
        if ($id) {
            try {
                $this->requestRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('The Request has been deleted.'));
                $resultRedirect->setPath('designnbuy_reseller/*/');
                return $resultRedirect;
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('The Request no longer exists.'));
                return $resultRedirect->setPath('designnbuy_reseller/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('designnbuy_reseller/request/edit', ['request_id' => $id]);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('There was a problem deleting the Request'));
                return $resultRedirect->setPath('designnbuy_reseller/request/edit', ['request_id' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a Request to delete.'));
        $resultRedirect->setPath('designnbuy_reseller/*/');
        return $resultRedirect;
    }
}
