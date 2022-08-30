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
class ProductDetail extends \Magento\Backend\Block\Widget\Grid\Container
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
        $this->_controller = 'adminhtml_productdetail';
        $this->_headerText = __('Product Detail Report');
        parent::_construct();
        $this->buttonList->remove('add');

        $this->addButton(
            'back_button',
            [
                'label' => __('Back'),
                'onclick' => 'setLocation(\'' . $this->getUrl('commissionreport/report/product') . '\')',
                'class' => 'back'
            ],
            -1
        );
    }

    /**
     * Get filter URL
     *
     * @return string
     */
    public function getFilterUrl()
    {
        $this->getRequest()->setParam('filter', null);
        return $this->getUrl('*/*/productdetail', ['_current' => true]);
    }
}
