<?php
namespace Designnbuy\Book\Observer;


use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class BookLayout implements  ObserverInterface
{

    protected $_registry;

    protected $_helper;

    public function __construct(
        \Magento\Framework\Registry $registry,
        \Designnbuy\Book\Helper\Data $helper,
        \Magento\Framework\App\State $appState
    )
    {       
        $this->_registry = $registry;
        $this->_helper = $helper;
        $this->appState = $appState;
    }
    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if($this->appState->getAreaCode() != \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE){
            $layout = $observer->getLayout();
            $_product = $this->_registry->registry('current_product');

            if ($_product && $this->_helper->isBookProduct($_product)) {
                $layout->getUpdate()->addHandle('catalog_product_view_book');
            }
        }

        return $this;
    }
}