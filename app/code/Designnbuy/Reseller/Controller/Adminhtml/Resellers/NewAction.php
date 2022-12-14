<?php
namespace Designnbuy\Reseller\Controller\Adminhtml\Resellers;

class NewAction extends \Magento\Backend\App\Action
{
    protected $resultForwardFactory;
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
    ) {
        $this->resultForwardFactory = $resultForwardFactory;
        parent::__construct($context);
    }

    public function execute() 
    {
        $resultForward = $this->resultForwardFactory->create();
        return $resultForward->forward('edit');
    }
    protected function _isAllowed()
    {
        return true;
    }
}
