<?php

namespace Designnbuy\Commission\Controller\Commission;

use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;

/**
 * Designer OverView
 */
class OverView extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory; 

    protected $_customerSession;
    
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;        
        parent::__construct($context);
    }

    public function execute()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerSession = $objectManager->get('Magento\Customer\Model\Session');
        if(!$customerSession->isLoggedIn()) {
           $resultRedirect = $this->resultRedirectFactory->create();
           $resultRedirect->setPath('customer/account/login');
           return $resultRedirect;
        }

        $resultPage = $this->resultPageFactory->create();
        return $resultPage;
    }
}
