<?php

namespace Designnbuy\Commission\Block\Adminhtml;

/**
 * Admin designer commission
 */
class Redemption extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_controller = 'adminhtml_commission';
        $this->_blockGroup = 'Designnbuy_Commission';
        $this->_headerText = __('Redemption');
        parent::_construct();
        $this->removeButton('add');
    }
}
