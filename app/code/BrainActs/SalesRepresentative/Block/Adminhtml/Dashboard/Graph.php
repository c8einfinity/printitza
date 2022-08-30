<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Block\Adminhtml\Dashboard;

use Magento\Backend\Block\Template\Context;

/**
 * Class Graph
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class Graph extends \Magento\Backend\Block\Template
{

    /**
     * @var string
     */
    protected $_template = 'dashboard/graph.phtml';//@codingStandardsIgnoreLine

    /**
     * @var \BrainActs\SalesRepresentative\Model\GraphReportFactory
     */
    private $graphReportFactory;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * Graph constructor.
     * @param Context $context
     * @param \BrainActs\SalesRepresentative\Model\GraphReportFactory $graphReportFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        Context $context,
        \BrainActs\SalesRepresentative\Model\GraphReportFactory $graphReportFactory,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->graphReportFactory = $graphReportFactory;
        $this->coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }

    /**
     * @param string $period
     * @return string
     *
     */

    public function prepareReportData()
    {
        $member = $this->coreRegistry->registry('salesrep_member');
        /** @var \BrainActs\SalesRepresentative\Model\GraphReport $report */
        $report = $this->graphReportFactory->create();
        return json_encode($report->prepareReportData('7d', $member));
    }

    public function getRequestUrl()
    {
        return $this->getUrl(
            'salesrep/dashboard/report',
            [
                '_current' => true
            ]
        );
    }
}
