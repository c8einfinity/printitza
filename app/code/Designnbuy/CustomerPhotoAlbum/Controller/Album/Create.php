<?php
namespace Designnbuy\CustomerPhotoAlbum\Controller\Album;
use Magento\Framework\Controller\ResultFactory;
class Create extends \Magento\Framework\App\Action\Action
{
    protected $_helper;

    protected $session;

    protected $_customerUrl;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $session,
        \Magento\Customer\Model\Url $customerUrl, 
        \Designnbuy\CustomerPhotoAlbum\Helper\Data $_helper
    ) {
        $this->_helper = $_helper;
        $this->session = $session;
        $this->_customerUrl = $customerUrl;
        parent::__construct($context);
    }

    public function execute()
    {
        $params = $this->getRequest()->getParams();
        if(!empty($params)) {
            $this->_helper->unSetProductParams();
            $this->_helper->setProductParams($params);
        }
        
        
        $this->_view->loadLayout();
        if ($block = $this->_view->getLayout()->getBlock('customer_customer')) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }
        $this->_view->getPage()->getConfig()->getTitle()->set(__('Upload Photos'));

        if ($block = $this->_view->getLayout()->getBlock('customer.account.link.back')) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }
        $this->_view->renderLayout();
    }
}