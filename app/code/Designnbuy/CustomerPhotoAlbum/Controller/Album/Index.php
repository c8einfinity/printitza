<?php
/**
 * Copyright © 2019 Bhavin Gehlot (bhavin.gehlot@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Nothing Is Impossible
 */
namespace Designnbuy\CustomerPhotoAlbum\Controller\Album;

use \Magento\Framework\Controller\ResultFactory;
class Index extends \Magento\Framework\App\Action\Action
{
    protected $session;

    protected $_customerUrl;

    protected $resultFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        ResultFactory $result,
        \Magento\Customer\Model\Session $session,
        \Magento\Customer\Model\Url $customerUrl
    ) {
        $this->session = $session;
        $this->resultFactory = $result;
        $this->_customerUrl = $customerUrl;
        parent::__construct($context);
    }
    public function execute()
    {
        if(!$this->session->isLoggedIn()){
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($this->_customerUrl->getLoginUrl());
            $this->messageManager->addError("Please login to create photo album");
            return $resultRedirect;
        }

        $this->_view->loadLayout();

        if ($block = $this->_view->getLayout()->getBlock('customer_customer')) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }
        $this->_view->getPage()->getConfig()->getTitle()->set(__('My Photos (Manage Albums)'));

        if ($block = $this->_view->getLayout()->getBlock('customer.account.link.back')) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }

        $this->_view->renderLayout();
    }
}