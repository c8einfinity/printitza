<?php

namespace Designnbuy\CustomerPhotoAlbum\Controller\Adminhtml\album;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Album extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPagee;

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

    /**
     * Index action
     *
     * @return void
     */
    public function execute()
    {
        /* error_reporting(-1);
        ini_set('display_errors', 'On'); */
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        //$resultPage->setActiveMenu('Designnbuy_CustomerPhotoAlbum::album');
        $resultPage->addBreadcrumb(__('Designnbuy'), __('Designnbuy'));
        $resultPage->addBreadcrumb(__('Manage item'), __('Manage Album'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Album'));

        return $resultPage;
    }
}
?>