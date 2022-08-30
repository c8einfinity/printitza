<?php
namespace Designnbuy\Canvas\Observer;


use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class CustomLayout implements  ObserverInterface
{

    protected $_registry;

    protected $_helper;

    public function __construct(
        \Magento\Framework\Registry $registry,
        \Designnbuy\Base\Helper\Data $helper
    )
    {       
        $this->_registry = $registry;
        $this->_helper = $helper;
    }
    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $action = $observer->getData('full_action_name');
        
        if ($action !== 'catalog_product_view' && $action != 'checkout_cart_configure' && $action != 'wishlist_index_configure') {
            return;
        }
        $layout = $observer->getLayout();
        $_product = $this->_registry->registry('current_product');
        if($observer->getEvent()->getName() == 'layout_load_before'){
            if ($_product && $_product->hasOptions() && count($_product->getOptions()) > 0 && $this->_helper->isCanvasProduct($_product)) {
                if($_product->getPageLayout() == "product-page-v2"){
                    $layout->getUpdate()->addHandle('catalog_product_view_version_two');
                } else {
                    $layout->getUpdate()->addHandle('catalog_product_view_custom');    
                }
                
            } else if ($_product && count($_product->getOptions()) == 0) {
                if($_product->getPageLayout() == "product-page-v2"){
                    $layout->getUpdate()->addHandle('catalog_product_view_version_two');
                }
                $layout->getUpdate()->addHandle('catalog_product_view_custom_without_option');
            }
        }
        if($observer->getEvent()->getName() == 'layout_generate_blocks_after'){
            if ($_product && $_product->hasOptions() && count($_product->getOptions()) > 0 && $this->_helper->isCanvasProduct($_product) && $_product->getAllowattachment() == 0) {
                $layout = $observer->getData('layout');
                $layout->unsetElement('product-extra-info-box2');
            }

        }


        return $this;
    }
}