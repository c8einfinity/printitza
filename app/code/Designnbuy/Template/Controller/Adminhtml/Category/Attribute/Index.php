<?php
/**
 * Template attribute index controller
 */

namespace Designnbuy\Template\Controller\Adminhtml\Category\Attribute;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(Context $context, PageFactory $resultPageFactory) 
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        
    }
    
    /*
     * Index action.
     * 
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /* @var Magento\Backend\Model\View\Result\Page $resultPage*/
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Designnbuy_Template::category_attributes');
        $resultPage->addBreadcrumb(__('Template Category Attribute'), __('Template Category'));
        $resultPage->addBreadcrumb(__('Manage Template Attribute'), __('Template Category Attributes'));
        $resultPage->getConfig()->getTitle()->prepend(__('Template Category Attributes'));
        return $resultPage;
    }
    
    /*
     * Is user allowed to view manage customer attribute grid.
     * 
     * @return bool
     */
    protected function _isAllowed() 
    {
        return $this->_authorization->isAllowed('Designnbuy_Template::category_attributes');
    }
}