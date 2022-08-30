<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Block\Adminhtml\OrderTicket\Create;

/**
 * Admin ORDERTICKET create form header
 */
class Header extends \Designnbuy\OrderTicket\Block\Adminhtml\OrderTicket\Create\AbstractCreate
{
    /**
     * Create new orderticket content
     *
     * @return string
     */
    protected function _toHtml()
    {
        $customerId = $this->getCustomerId();
        $storeId = $this->getStoreId();
        $out = '';
        if ($customerId && $storeId) {
            $storeName = $this->_storeManager->getStore($storeId)->getName();
            $customerName = $this->getCustomerName();
            $out .= __('Create New Order Ticket for %1 in %2', $customerName, $storeName);
        } elseif ($customerId) {
            $out .= __('Create New Order Ticket for %1', $this->getCustomerName());
        } else {
            $out .= __('Create New Order Ticket');
        }
        $out = $this->escapeHtml($out);
        $out = '<h3 class="icon-head head-sales-order">' . $out . '</h3>';
        return $out;
    }
}
