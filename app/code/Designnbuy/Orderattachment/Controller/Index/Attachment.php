<?php

namespace Designnbuy\Orderattachment\Controller\Index;

use Magento\Framework\Controller\ResultFactory;
/**
 * Class Index
 * @package Designnbuy\Orderattachment\Controller\Index
 */
class Attachment extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $coreSession;

    /**
     * Index constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Session\SessionManagerInterface $coreSession
    ) {
        $this->coreSession = $coreSession;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if (!empty($this->getRequest()->getParams())) {
            
            $this->coreSession->setRefererUrl($this->_redirect->getRefererUrl());

            $this->_view->loadLayout();
            $this->_view->renderLayout();
        } else {
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            
            $refereUrl = $this->_redirect->getRefererUrl();
            if($this->coreSession->getRefererUrl()){
                $refereUrl = $this->coreSession->getRefererUrl();
                $this->coreSession->unsRefererUrl();
            }
            
            $resultRedirect->setUrl($refereUrl);
            return $resultRedirect;
        }
    }
}


