<?php
/**
 * Copyright © 2019 Designnbuy WebToPrint Pvt. Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 * ALWAYS DO BETTER @a
 */
namespace Drc\Storepickup\Block\Adminhtml;

class Holidays extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_holidays';
        $this->_blockGroup = 'Drc_Storepickup';
        $this->_headerText = __('Holidays');
        $this->_addButtonLabel = __('Create New Holidays');
        parent::_construct();
    }
}
