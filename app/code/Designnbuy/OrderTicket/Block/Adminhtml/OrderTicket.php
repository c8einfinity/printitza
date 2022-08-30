<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * ORDERTICKET Adminhtml Block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Designnbuy\OrderTicket\Block\Adminhtml;

class OrderTicket extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Initialize ORDERTICKET management page
     *
     * @return void
     */
    public function _construct()
    {
        $this->_controller = 'adminhtml_OrderTicket';
        $this->_blockGroup = 'Designnbuy_OrderTicket';
        $this->_headerText = __('Order Tickets');
        $this->_addButtonLabel = __('New Order Ticket Request');

        parent::_construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        $parent = $this->getParentBlock();
        if ($parent instanceof Customer\Edit\Tab\OrderTicket || $parent instanceof Order\View\Tab\OrderTicket) {
            $this->removeButton('add');
        }
        return parent::_prepareLayout();
    }

    /**
     * Get URL for New ORDERTICKET Button
     *
     * @return string
     */
    public function getCreateUrl()
    {
        return $this->getUrl('adminhtml/*/new');
    }
}
