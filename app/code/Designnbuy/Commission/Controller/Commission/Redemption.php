<?php

namespace Designnbuy\Commission\Controller\Commission;

use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;

/**
 * Design home page view
 */
class Redemption extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory; 

    protected $_customerSession;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param PageFactory $resultPageFactory
     */
    
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;        
        parent::__construct($context);
    }

    /**
     * View Design idea action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    
    public function execute()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerSession = $objectManager->get('Magento\Customer\Model\Session');
        if(!$customerSession->isLoggedIn()) {
           $resultRedirect = $this->resultRedirectFactory->create();
           $resultRedirect->setPath('customer/account/login');
           return $resultRedirect;
        }

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        return $resultPage;
    }
}
