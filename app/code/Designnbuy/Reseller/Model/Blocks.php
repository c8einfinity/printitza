<?php
namespace Designnbuy\Reseller\Model;


class Blocks extends \Designnbuy\Reseller\Model\Observer\AbstractClass {

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager = null;

    public function __construct(
        \Designnbuy\Reseller\Model\Admin $reseller,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\ObjectManager\FactoryInterface $factory,
        \Magento\Framework\ObjectManager\ConfigInterface $config

    )
    {
        $this->_storeManager = $storeManager;
        $this->_objectManager = $objectManager;
        parent::__construct($reseller);
    }

    /**
     * Restrict product grid container
     *
     * @param Varien_Event_Observer $observer
     */
    public function widgetProductGridContainer($container)
    {
        if($this->_reseller->isResellerAdmin()) {
            $container->removeButton('add_new');
        }
    }

    public function removeCorporateButtons($container)
    {
        if($this->_reseller->isResellerAdmin()) {
            $container->removeButton('save');
            $container->removeButton('delete');
            $container->removeButton('back');
            $container->removeButton('reset');
        }
    }

    public function removeProductColumn($observer)
    {
        $block = $observer->getEvent()->getBlock();
        if ($block instanceof \Magento\Backend\Block\Widget\Grid\Extended) {
            foreach ($block->getColumns() as $columnName => $column):
                $block->removeColumn($column);
            endforeach;
        }
    }

    public function removeProductTabs($observer)
    {
        $block = $observer->getEvent()->getBlock();
        echo get_class($block);
        if ($tabBlock instanceof \Magento\Catalog\Block\Adminhtml\Product\Edit\Tabs) {
            $tab_ids = $tabBlock->getTabsIds();

        }
    }



}
