<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Block\Adminhtml\OrderTicket\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Initialize ORDERTICKET edit page tabs
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('orderticket_info_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Order Ticket Information'));
    }
}
