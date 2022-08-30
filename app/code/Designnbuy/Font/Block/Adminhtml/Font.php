<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Font\Block\Adminhtml;

/**
 * Admin font font
 */
class Font extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_font';
        $this->_blockGroup = 'Designnbuy_Font';
        $this->_headerText = __('Font');
        $this->_addButtonLabel = __('Add New Font');
        parent::_construct();
    }
}
