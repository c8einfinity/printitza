<?php

namespace Designnbuy\Commission\Controller\Commission;

use Magento\Customer\Model\Session;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;

/**
 * Design Report
 */
class DesignDetailReport extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory; 

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Session $customerSession
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->session = $customerSession;        
        parent::__construct($context);
    }

    public function execute()
    {
        if (!$this->session->isLoggedIn()) {
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('customer/account/login');
            return $resultRedirect;
        }
        
        $resultPage = $this->resultPageFactory->create();
        return $resultPage;
    }
}
