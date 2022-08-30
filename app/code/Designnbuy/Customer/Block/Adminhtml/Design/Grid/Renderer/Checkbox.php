<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Customer designs grid checkbox item renderer
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Designnbuy\Customer\Block\Adminhtml\Design\Grid\Renderer;

class Checkbox extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Renders grid column
     *
     * @param   \Magento\Framework\DataObject $row
     * @return  string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        if ($row->getDesignStatus() == \Designnbuy\Customer\Model\Design::STATUS_SUBSCRIBED) {
            return '<input type="checkbox" name="design[]" value="' .
                $row->getId() .
                '" class="designCheckbox"/>';
        } else {
            return '';
        }
    }
}
