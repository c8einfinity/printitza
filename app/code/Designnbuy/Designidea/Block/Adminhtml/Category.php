<?php

namespace Designnbuy\Designidea\Block\Adminhtml;

/**
 * Designidea grid container
 * @category Designnbuy
 * @package  Designnbuy_Designidea
 * @module   Designidea
 * @author   Designnbuy Developer
 */
class Category extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Internal constructor, that is called from real constructor
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_category';
        $this->_blockGroup = 'Designnbuy_Designidea';
        $this->_headerText = __('Category');
        $this->_addButtonLabel = __('Add New Category');

        parent::_construct();
    }
}
