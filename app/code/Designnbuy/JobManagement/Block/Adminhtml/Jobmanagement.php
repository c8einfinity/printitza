<?php

namespace Designnbuy\JobManagement\Block\Adminhtml;

class Jobmanagement extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_controller = 'adminhtml_jobmanagement';
        $this->_blockGroup = 'Designnbuy_JobManagement';
        $this->_headerText = __('Job Management');                
        parent::_construct();
        $this->buttonList->remove('add');
    }
}
