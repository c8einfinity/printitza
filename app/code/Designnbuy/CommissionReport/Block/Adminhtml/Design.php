<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\CommissionReport\Block\Adminhtml;

/**
 * Adminhtml sales report page content block
 *
 * @api
 * @author      Magento Core Team <core@magentocommerce.com>
 * @since 100.0.2
 */
class Design extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Template file
     *
     * @var string
     */
    protected $_template = 'report/grid/container.phtml';

    /**
     * {@inheritdoc}
     */

    protected function _construct()
    {
        $this->_blockGroup = 'Designnbuy_CommissionReport';
        $this->_controller = 'adminhtml_design';
        $this->_headerText = __('Total Design Report');
        parent::_construct();

        $this->buttonList->remove('add');
    }

    /**
     * Get filter URL
     *
     * @return string
     */
    public function getFilterUrl()
    {
        $this->getRequest()->setParam('filter', null);
        return $this->getUrl('*/*/design', ['_current' => true]);
    }
}
