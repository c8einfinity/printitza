<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace BrainActs\SalesRepresentative\Block\Adminhtml\Report;

/**
 * Class Products
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class Products extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Template
     *
     * @var string
     */
    protected $_template = 'report/grid/container.phtml';//@codingStandardsIgnoreLine


    protected function _construct()//@codingStandardsIgnoreLine
    {
        $this->_blockGroup = 'BrainActs_SalesRepresentative';
        $this->_controller = 'adminhtml_report_products';
        $this->_headerText = __('Products Report');
        parent::_construct();

        $this->buttonList->remove('add');
        $this->addButton(
            'filter_form_submit',
            ['label' => __('Show Report'), 'onclick' => 'filterFormSubmit()', 'class' => 'primary']
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
        return $this->getUrl('*/*/products', ['_current' => true]);
    }
}
