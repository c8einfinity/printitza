<?php

namespace Designnbuy\CommissionReport\Controller\Adminhtml\Report;

/**
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 * @api
 * @since 100.0.2
 */
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Product extends \Magento\Backend\App\Action
{
    /**
    * @var PageFactory
    */
    protected $resultPageFactory;

    /**
    * @param Context $context
    * @param PageFactory $resultPageFactory
    */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Designnbuy_CommissionReport::productreport');
    }

    /**
    * Ecommerce List action
    *
    * @return void
    */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu(
        'Designnbuy_Designer::marketplace'
        )->addBreadcrumb(
            __('Commission'),__('Commission')
        )->addBreadcrumb(
            __('Manage Product Report'),
            __('Manage Product Report')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Product Report'));
        return $resultPage;
    }
}
