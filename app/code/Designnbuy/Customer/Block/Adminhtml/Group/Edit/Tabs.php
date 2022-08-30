<?php
namespace Designnbuy\Customer\Block\Adminhtml\Group\Edit;

/**
 * Class Tabs
 * @package Designnbuy\Productattach\Block\Adminhtml\Productattach\Edit
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('page_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Group Information'));
    }
}