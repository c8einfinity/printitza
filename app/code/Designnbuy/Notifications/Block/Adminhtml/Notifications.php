<?php
namespace Designnbuy\Notifications\Block\Adminhtml;

/**
 * Admin Notifications
 */
class Notifications extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_controller = 'adminhtml_notifications';
        $this->_blockGroup = 'Designnbuy_Notifications';
        $this->_headerText = __('Notifications');
        parent::_construct();
        $this->removeButton('add');
    }
}
