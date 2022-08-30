<?php
/**
 * Copyright © 2019 Bhavin Gehlot (bhavin.gehlot@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Nothing Is Impossible
 */
namespace Designnbuy\CustomerPhotoAlbum\Controller\Album;
use Magento\Framework\Controller\ResultFactory;
class Edit extends \Magento\Framework\App\Action\Action
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
        $this->_view->loadLayout();
        if ($block = $this->_view->getLayout()->getBlock('customer_customer')) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }
        if($this->getRequest()->getParam('id')){
            $this->_view->getPage()->getConfig()->getTitle()->set(__($this->_helper->getAlbumTitle($this->getRequest()->getParam('id'))));
        }
        if ($block = $this->_view->getLayout()->getBlock('customer.account.link.back')) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }
        $this->_view->renderLayout();
    }
}