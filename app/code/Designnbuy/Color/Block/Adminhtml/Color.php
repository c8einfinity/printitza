<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Color\Block\Adminhtml;

/**
 * Admin color color
 */
class Color extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_color';
        $this->_blockGroup = 'Designnbuy_Color';
        $this->_headerText = __('Color');
        $this->_addButtonLabel = __('Add New Color');
        parent::_construct();
    }
}
