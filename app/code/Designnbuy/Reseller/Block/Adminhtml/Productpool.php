<?php
namespace Designnbuy\Reseller\Block\Adminhtml;

/**
 * Admin Reseller Productpool
 */
class Productpool extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_controller = 'adminhtml_productpool';
        $this->_blockGroup = 'Designnbuy_Reseller';
        $this->_headerText = __('Productpool');
        $this->_addButtonLabel = __('Add New Pool');
        parent::_construct();
    }
}
