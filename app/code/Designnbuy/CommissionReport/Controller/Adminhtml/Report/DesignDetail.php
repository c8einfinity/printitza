<?php

namespace Designnbuy\CommissionReport\Controller\Adminhtml\Report;

/**
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 * @api
 * @since 100.0.2
 */
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class DesignDetail extends \Magento\Backend\App\Action
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
        return $this->_authorization->isAllowed('Designnbuy_CommissionReport::commissionreport_report');
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
            __('Manage Commission Report'),
            __('Manage Commission Report')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Design Detail Report'));
        return $resultPage;
    }
}
