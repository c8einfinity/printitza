<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Controller\Adminhtml\OrderTicket;

class Delete extends \Designnbuy\OrderTicket\Controller\Adminhtml\OrderTicket
{
    /**
     * Delete orderticket
     *
     * @return void
     */
    public function execute()
    {
        $this->_redirect('adminhtml/*/');
    }
}
