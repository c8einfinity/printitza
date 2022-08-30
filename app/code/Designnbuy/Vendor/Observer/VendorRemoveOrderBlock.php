<?php

namespace Designnbuy\Vendor\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class VendorRemoveOrderBlock implements ObserverInterface
{
    protected $_scopeConfig;

    /**
     * @var \Designnbuy\Vendor\Helper\Data
     */
    protected $vendorData;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Designnbuy\Vendor\Helper\Data $vendorData
    ) {
        $this->_request = $context->getRequest();
        $this->_scopeConfig = $scopeConfig;
        $this->vendorData = $vendorData;
    }

    public function execute(Observer $observer)
    {
        if($this->vendorData->getVendorUser()){
            //$fullActionName = $observer->getFullActionName();
            if ($this->_request->getFullActionName() == 'sales_order_index') {
                $gridBlock = $observer->getEvent()->getBlock('sales_order_grid_block');

                if($gridBlock instanceof \Magento\Ui\Component\Control\Container){
                    $gridBlock->getButtonItem()->setData('style','display:none');
                }
                return;
            }
            if ($this->_request->getFullActionName() != 'sales_order_view') {
                return;
            }
            
            /** @var \Magento\Framework\View\Layout $layout */
            $tabBlock = $observer->getEvent()->getBlock('sales_order_tabs');
           

            //$tabBlock = $layout->getBlock('sales_order_tabs');
            //$tabBlock = $observer->getLayout()->getBlock('sales_order_tabs');
            $tab_ids = [];
            if ($tabBlock instanceof \Magento\Sales\Block\Adminhtml\Order\View\Tabs) {
                $tab_ids = $tabBlock->getTabsIds();
                foreach ($tab_ids as $tab){
                    if($tab != 'order_info' && $tab != 'order_orderticket'){
                        $tabBlock->removeTab($tab);
                    }
                }
            }
            return;





            /*foreach ($layout->getChildNames('order_tab_info') as $childBlock) {
                if ($childBlock == 'order_info') {
                    $layout->unsetChild('order_tab_info', $childBlock);
                }
                if ($childBlock == 'order_payment') {
                    $layout->unsetChild('order_tab_info', $childBlock);
                }
                if ($childBlock == 'payment_additional_info') {
                    $layout->unsetChild('order_tab_info', $childBlock);
                }
                if ($childBlock == 'order_shipping_view') {
                    $layout->unsetChild('order_tab_info', $childBlock);
                }
            }*/
        }
    }
}
