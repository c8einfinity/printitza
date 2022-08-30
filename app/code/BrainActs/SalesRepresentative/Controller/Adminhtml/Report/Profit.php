<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace BrainActs\SalesRepresentative\Controller\Adminhtml\Report;

use BrainActs\SalesRepresentative\Controller\Adminhtml\Report as ReportController;

/**
 * Class Profit
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class Profit extends ReportController
{
    /**
     * Report action
     *
     * @return void
     */
    public function execute()
    {

        $this->_initAction()->_setActiveMenu(
            'BrainActs_SalesRepresentative::report_profit'
        )->_addBreadcrumb(
            __('Report By SR'),
            __('Report By SR')
        );
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Report By SR'));

        $gridBlock = $this->_view->getLayout()->getBlock('adminhtml_report_profit.grid');
        $filterFormBlock = $this->_view->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction([$gridBlock, $filterFormBlock]);

        $this->_view->renderLayout();
    }
}
