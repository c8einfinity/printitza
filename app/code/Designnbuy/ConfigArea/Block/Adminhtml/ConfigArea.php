<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\ConfigArea\Block\Adminhtml;

/**
 * Admin configarea configarea
 */
class ConfigArea extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_configarea';
        $this->_blockGroup = 'Designnbuy_ConfigArea';
        $this->_headerText = __('Design Area');
        $this->_addButtonLabel = __('Add New Design Area');
        parent::_construct();
    }
}
