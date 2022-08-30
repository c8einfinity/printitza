<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Designnbuy\Vendor\Controller\Adminhtml\User;

/**
 * Class Index. Set active menu, title, add breadcrumb
 * @package MageWorx\OptionInventory\Controller\Adminhtml\Report
 */
class Product extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Report list.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Designnbuy_Vendor::vendor');
        $resultPage->getConfig()->getTitle()->prepend(__('Vendor Products'));
        $resultPage->addBreadcrumb(__('Vendor'), __('Products'));
        return $resultPage;
    }
}
