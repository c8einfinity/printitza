<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Customer\Controller\Adminhtml\Design;

class Index extends \Designnbuy\Customer\Controller\Adminhtml\Design
{
    /**
     * Customer designs page
     *
     * @return void
     */
    public function execute()
    {
        if ($this->getRequest()->getParam('ajax')) {
            $this->_forward('grid');
            return;
        }

        $this->_view->loadLayout();

        $this->_setActiveMenu('Designnbuy_Customer::customer_design');
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Customer Designs'));

        $this->_addBreadcrumb(__('Customer'), __('Customer'));
        $this->_addBreadcrumb(__('Designs'), __('Designs'));

        $this->_view->renderLayout();
    }
}
