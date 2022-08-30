<?php


namespace Designnbuy\Vendor\Controller\Adminhtml\User;

class Index extends \Magento\Backend\App\Action
{

    protected $resultPageFactory;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Designnbuy\Vendor\Helper\Data $vendorData
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->vendorData = $vendorData;
        parent::__construct($context);
    }

    /**
     * Index action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if($this->vendorData->isVendor()){
            $this->_redirect('*/*/edit', ['id'=> $this->vendorData->getVendorUser()->getId() ,'_current' => true, 'active_tab' => '']);
        }

        $resultPage = $this->resultPageFactory->create();
            $resultPage->getConfig()->getTitle()->prepend(__("Manage Vendors"));
            return $resultPage;
    }
}
