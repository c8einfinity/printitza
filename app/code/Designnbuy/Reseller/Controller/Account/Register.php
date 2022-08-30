<?php
namespace  Designnbuy\Reseller\Controller\Account;

class Register extends \Magento\Framework\App\Action\Action
{
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}