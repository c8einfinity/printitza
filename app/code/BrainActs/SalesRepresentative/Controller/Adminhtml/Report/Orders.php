<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace BrainActs\SalesRepresentative\Controller\Adminhtml\Report;

use BrainActs\SalesRepresentative\Controller\Adminhtml\Report as ReportController;

/**
 * Class Orders
 * @author BrainActs Core Team <support@brainacts.com>
 */
class Orders extends ReportController
{
    /**
     * Report action
     *
     * @return void
     */
    public function execute()
    {

        $this->_initAction()->_setActiveMenu(
            'BrainActs_SalesRepresentative::report_orders'
        )->_addBreadcrumb(
            __('Orders Report'),
            __('Orders Report')
        );
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Orders Report'));

        $gridBlock = $this->_view->getLayout()->getBlock('adminhtml_report_orders.grid');
        $filterFormBlock = $this->_view->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction([$gridBlock, $filterFormBlock]);

        $this->_view->renderLayout();
    }
}
