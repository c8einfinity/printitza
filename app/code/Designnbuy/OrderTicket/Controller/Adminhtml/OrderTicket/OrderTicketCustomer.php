<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Controller\Adminhtml\OrderTicket;

class OrderTicketCustomer extends \Designnbuy\OrderTicket\Controller\Adminhtml\OrderTicket
{
    /**
     * Generate ORDERTICKET grid for ajax request from customer page
     *
     * @return void
     */
    public function execute()
    {
        $customerId = intval($this->getRequest()->getParam('id'));
        if ($customerId) {
            $this->getResponse()->setBody(
                $this->_view->getLayout()->createBlock(
                    'Designnbuy\OrderTicket\Block\Adminhtml\Customer\Edit\Tab\OrderTicket'
                )->setCustomerId(
                    $customerId
                )->toHtml()
            );
        }
    }
}
