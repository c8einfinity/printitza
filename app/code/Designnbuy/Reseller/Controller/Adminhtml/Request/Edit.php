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

class Edit extends \Designnbuy\Reseller\Controller\Adminhtml\Request
{
    /**
     * Initialize current Request and set it in the registry.
     *
     * @return int
     */
    protected function initRequest()
    {
        $requestId = $this->getRequest()->getParam('request_id');
        $this->coreRegistry->register(\Designnbuy\Reseller\Controller\RegistryConstants::CURRENT_REQUEST_ID, $requestId);

        return $requestId;
    }

    /**
     * Edit or create Request
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $requestId = $this->initRequest();

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Designnbuy_Reseller::reseller_request');
        $resultPage->getConfig()->getTitle()->prepend(__('Requests'));
        $resultPage->addBreadcrumb(__('Manage Reseller Request'), __('Manage Reseller Request'));
        $resultPage->addBreadcrumb(__('Requests'), __('Requests'), $this->getUrl('designnbuy_reseller/request'));

        if ($requestId === null) {
            $resultPage->addBreadcrumb(__('New Request'), __('New Request'));
            $resultPage->getConfig()->getTitle()->prepend(__('New Request'));
        } else {
            $resultPage->addBreadcrumb(__('Edit Request'), __('Edit Request'));
            $resultPage->getConfig()->getTitle()->prepend(
                $this->requestRepository->getById($requestId)->getStore_code()
            );
        }
        return $resultPage;
    }
}
