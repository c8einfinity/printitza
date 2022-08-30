<?php
/**
 * Copyright © Designnbuy (support@designnbuy.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Designnbuy\Sheet\Block\Adminhtml;

/**
 * Admin sheet size
 */
class Size extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_size';
        $this->_blockGroup = 'Designnbuy_Sheet';
        $this->_headerText = __('Size');
        $this->_addButtonLabel = __('Add New Size');

        parent::_construct();
        if (!$this->_authorization->isAllowed("Designnbuy_Sheet::size_save")) {
            $this->removeButton('add');
        }
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
//        if ($this->_authorization->isAllowed("Designnbuy_Sheet::import")) {
//            $onClick = "setLocation('" . $this->getUrl('*/import') . "')";
//
//            $this->getToolbar()->addChild(
//                'options_button',
//                \Magento\Backend\Block\Widget\Button::class,
//                ['label' => __('Import Sizes'), 'onclick' => $onClick]
//            );
//        }
        return parent::_prepareLayout();
    }
}
