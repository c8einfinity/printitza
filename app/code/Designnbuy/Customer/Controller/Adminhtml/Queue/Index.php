<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Customer\Controller\Adminhtml\Queue;

class Index extends \Designnbuy\Customer\Controller\Adminhtml\Queue
{
    /**
     * Queue list action
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

        $this->_setActiveMenu('Designnbuy_Customer::designnbuy_customer_queue');
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Customer Queue'));
        $this->_addBreadcrumb(__('Customer Queue'), __('Customer Queue'));

        $this->_view->renderLayout();
    }
}
