<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Clipart\Block\Adminhtml;

/**
 * Admin clipart clipart
 */
class Clipart extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_clipart';
        $this->_blockGroup = 'Designnbuy_Clipart';
        $this->_headerText = __('Clipart');
        $this->_addButtonLabel = __('Add New Clipart');
        parent::_construct();
    }
}
