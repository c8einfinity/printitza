<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Customer\Controller\Adminhtml\Template;

class Drop extends \Designnbuy\Customer\Controller\Adminhtml\Template
{
    /**
     * Drop Customer Template
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout('customer_template_preview_popup');
        $this->_view->renderLayout();
    }
}
