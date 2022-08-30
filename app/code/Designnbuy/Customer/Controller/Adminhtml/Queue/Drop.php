<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Customer\Controller\Adminhtml\Queue;

class Drop extends \Designnbuy\Customer\Controller\Adminhtml\Queue
{
    /**
     * Drop Customer queue template
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout('designnbuy_customer_queue_preview_popup');
        $this->_view->renderLayout();
    }
}
