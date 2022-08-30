<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Background\Block\Adminhtml;

/**
 * Admin background background
 */
class Background extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_background';
        $this->_blockGroup = 'Designnbuy_Background';
        $this->_headerText = __('Background');
        $this->_addButtonLabel = __('Add New Background');
        parent::_construct();
    }
}
