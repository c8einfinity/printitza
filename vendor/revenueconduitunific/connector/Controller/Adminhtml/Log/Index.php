<?php

namespace Unific\Connector\Controller\Adminhtml\Log;

use Magento\Framework\Controller\ResultFactory;
use Unific\Connector\Controller\Adminhtml\Log;

class Index extends \Magento\Backend\App\Action
{
    protected $resultPageFactory = false;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend((__('Connector Log')));

        return $resultPage;
    }
}
