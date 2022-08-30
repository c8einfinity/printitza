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

class Index extends \Designnbuy\Reseller\Controller\Adminhtml\Request
{
    /**
     * Requests list.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Designnbuy_Base::network');
        $resultPage->getConfig()->getTitle()->prepend(__('Reseller Requests'));
        $resultPage->addBreadcrumb(__('Manage Reseller Request'), __('Manage Reseller Request'));
        $resultPage->addBreadcrumb(__('Reseller Requests'), __('Reseller Requests'));
        return $resultPage;
    }
}
