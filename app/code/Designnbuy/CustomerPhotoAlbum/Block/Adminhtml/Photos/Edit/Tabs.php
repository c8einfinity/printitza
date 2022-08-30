<?php
namespace Designnbuy\CustomerPhotoAlbum\Block\Adminhtml\Photos\Edit;

/**
 * Admin page left menu
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('photos_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Photos Information'));
    }
}