<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\PrintingMethod\Block\Adminhtml;

/**
 * Admin printingmethod printingmethod
 */
class SquareArea extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_squarearea';
        $this->_blockGroup = 'Designnbuy_PrintingMethod';
        $this->_headerText = __('Square Area');
        $this->_addButtonLabel = __('Add New Square Area');
        parent::_construct();
    }
}
