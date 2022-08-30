<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Customer\Controller\Adminhtml\Problem;

class Index extends \Designnbuy\Customer\Controller\Adminhtml\Problem
{
    /**
     * Customer problems report page
     *
     * @return void
     */
    public function execute()
    {
        if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }

        $this->_view->loadLayout();
        $this->_view->getLayout()->getMessagesBlock()->setMessages($this->messageManager->getMessages(true));

        $this->_setActiveMenu('Designnbuy_Customer::customer_problem');
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Customer Problems Report'));
        $this->_addBreadcrumb(__('Customer Problem Reports'), __('Customer Problem Reports'));

        $this->_view->renderLayout();
    }
}
