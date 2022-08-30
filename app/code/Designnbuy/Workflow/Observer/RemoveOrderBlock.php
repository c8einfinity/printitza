<?php

namespace Designnbuy\Workflow\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class RemoveOrderBlock implements ObserverInterface
{
    protected $_scopeConfig;

    /**
     * @var \Designnbuy\Workflow\Helper\Data
     */
    protected $workflowData;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Designnbuy\Workflow\Helper\Data $workflowData
    ) {
        $this->_request = $context->getRequest();
        $this->_scopeConfig = $scopeConfig;
        $this->workflowData = $workflowData;
    }

    public function execute(Observer $observer)
    {

        if($this->workflowData->getWorkflowUser()) {
            $workFlowRole = $this->workflowData->getWorkflowUserRole();

            if ($this->_request->getFullActionName() == 'sales_order_index') {
                $gridBlock = $observer->getEvent()->getBlock('sales_order_grid_block');

                if($gridBlock instanceof \Magento\Ui\Component\Control\Container){
                    $gridBlock->getButtonItem()->setData('style','display:none');
                }
                return;
            }


            $fullActionName = $observer->getFullActionName();

            if ($fullActionName != 'sales_order_view') {
                return;
            }

            /** @var \Magento\Framework\View\Layout $layout */
            $layout = $observer->getLayout();
            $tabBlock = $observer->getLayout()->getBlock('sales_order_tabs');
            $tab_ids = [];
            if ($tabBlock instanceof \Magento\Sales\Block\Adminhtml\Order\View\Tabs) {
                $tab_ids = $tabBlock->getTabsIds();
            }
            foreach ($tab_ids as $tab){
                if($tab != 'order_info'){
                    $tabBlock->removeTab($tab);
                }
            }
            $accesses = $workFlowRole->getAccesses();

            foreach ($layout->getChildNames('order_tab_info') as $childBlock) {
                if ($childBlock == 'order_info') {
                    if(isset($accesses) && !empty($accesses) && !in_array('view_customer_details',$accesses)){
                        $layout->unsetChild('order_tab_info', $childBlock);
                    }
                }
                if ($childBlock == 'order_payment') {
                    if(isset($accesses) && !empty($accesses) && !in_array('view_payment_details',$accesses)){
                        $layout->unsetChild('order_tab_info', $childBlock);
                    }
                }
                if ($childBlock == 'payment_additional_info') {
                    if(isset($accesses) && !empty($accesses) && !in_array('view_payment_details',$accesses)) {
                        $layout->unsetChild('order_tab_info', $childBlock);
                    }
                }
                if ($childBlock == 'order_shipping_view') {
                    if(isset($accesses) && !empty($accesses) && !in_array('view_payment_details',$accesses)) {
                        $layout->unsetChild('order_tab_info', $childBlock);
                    }
                }
            }


            $block = $observer->getLayout()->getBlock('column_output');
            if($workFlowRole && $block instanceof \Designnbuy\Base\Block\Adminhtml\Sales\Order\Items\Column\Output){
                $workFlowRole = $this->workflowData->getWorkflowUserRole();
                $accesses = $workFlowRole->getAccesses();
                if(isset($accesses) && !empty($accesses) && !in_array('download_files',$accesses)){
                    $block->setTemplate('');
                }

            }
        }




        return;

        /*$block = $layout->getBlock('extra_customer_info');

        if ($block) {
            $layout->unsetElement('extra_customer_info');
            $remove = $this->_scopeConfig->getValue(
                'dashboard/settings/remove',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );

            if ($remove) {
                $layout->unsetElement('dashboard');
            }
        }*/





        /*$layout = $observer->getLayout();
        $block = $layout->getBlock('admin.product.options');
        if ($block && ($block instanceof \Magento\Catalog\Block\Adminhtml\Product\Edit\Tab\Options)) {
            $block->addChild(
                'mageworx_option_groups',
                'MageWorx\OptionTemplates\Block\Adminhtml\Product\Edit\Tab\GroupSelect'
            );
        }*/
    }
}
