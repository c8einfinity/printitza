<?php

namespace Designnbuy\CustomerPhotoAlbum\Block\Adminhtml\Photos\Edit\Tab;

/**
 * Photos edit form main tab
 */
class Required extends \Magento\Framework\Data\Form\Element\Image
{
    protected function _getDeleteCheckbox()
    {
        return '';
    }
}