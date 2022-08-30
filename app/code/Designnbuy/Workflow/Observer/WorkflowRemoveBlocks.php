<?php

namespace Designnbuy\Workflow\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class WorkflowRemoveBlocks implements ObserverInterface
{
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\App\ViewInterface
     */
    protected $_view;

    /**
     * @var \Designnbuy\Workflow\Helper\Data
     */
    protected $workflowData;

    public function __construct(
        \Magento\Framework\App\ViewInterface $view,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Designnbuy\Workflow\Helper\Data $workflowData
    ) {
        $this->_view = $view;
        $this->_scopeConfig = $scopeConfig;
        $this->workflowData = $workflowData;
    }

    public function execute(Observer $observer)
    {
        if(!$this->workflowData->getWorkflowUser()) {

            /*$listBlock = $this->_view->getLayout()->getBlock('order_list');
            $listBlock->removeButton('add');*/
        }

    }
}
