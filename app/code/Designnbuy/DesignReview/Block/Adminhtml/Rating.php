<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\DesignReview\Block\Adminhtml;

/**
 * Ratings grid
 *
 * @api
 * @author      Magento Core Team <core@magentocommerce.com>
 * @since 100.0.2
 */
class Rating extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml';
        $this->_blockGroup = 'Designnbuy_DesignReview';
        $this->_headerText = __('Manage Ratings');
        $this->_addButtonLabel = __('Add New Rating');
        parent::_construct();
    }
}
