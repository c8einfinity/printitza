<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Customer\Controller\Adminhtml\Template;

class Index extends \Designnbuy\Customer\Controller\Adminhtml\Template
{
    /**
     * View Templates list
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
        $this->_setActiveMenu('Designnbuy_Customer::customer_template');
        $this->_addBreadcrumb(__('Customer Templates'), __('Customer Templates'));
        $this->_addContent(
            $this->_view->getLayout()->createBlock('Designnbuy\Customer\Block\Adminhtml\Template', 'template')
        );
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Customer Templates'));
        $this->_view->renderLayout();
    }
}
