<?php

namespace Designnbuy\Template\Block\Adminhtml;

/**
 * Template grid container
 * @category Designnbuy
 * @package  Designnbuy_Template
 * @module   Template
 * @author   Designnbuy Developer
 */
class Layout extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Internal constructor, that is called from real constructor
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_layout';
        $this->_blockGroup = 'Designnbuy_Template';
        $this->_headerText = __('Templates');
        $this->_addButtonLabel = __('Add New Layout');

        parent::_construct();
    }
}
