<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Controller\Adminhtml\OrderTicket;

class Chooseorder extends \Designnbuy\OrderTicket\Controller\Adminhtml\OrderTicket
{
    /**
     * Choose Order action during new ORDERTICKET creation
     *
     * @return void
     */
    public function execute()
    {
        $this->_initCreateModel();
        $this->_initAction();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('New Order Ticket'));
        $this->_view->renderLayout();
    }
}
