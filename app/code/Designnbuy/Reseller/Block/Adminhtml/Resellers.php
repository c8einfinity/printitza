<?php

namespace Designnbuy\Reseller\Block\Adminhtml;
class Resellers extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
    	$this->_blockGroup = 'Designnbuy_Reseller';
        $this->_controller = 'adminhtml_resellers';
        $this->_headerText = __('Resellers');
        parent::_construct();
    }
    protected function _addNewButton()
    {
        return '';
    }
}