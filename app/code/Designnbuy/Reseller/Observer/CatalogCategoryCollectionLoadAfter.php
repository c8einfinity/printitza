<?php
namespace Designnbuy\Reseller\Observer;

class CatalogCategoryCollectionLoadAfter implements \Magento\Framework\Event\ObserverInterface
{
    private $eventManager;
    
    private $_storeManager;

    public function __construct(
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->eventManager = $eventManager;
        $this->_storeManager = $storeManager;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        return;
        /** @var \Magento\Catalog\Model\ResourceModel\Category\Collection $categoryCollection */
        $filteredCategoryCollection = $observer->getCategoryCollection();
        $categoryCollection = clone $filteredCategoryCollection;
        $storeCode = $this->_storeManager->getStore()->getCode();
        $filteredCategoryCollection->removeAllItems(); 

        /** @var \Magento\Catalog\Model\Category $category */
        foreach ($categoryCollection as $category) {
            
            $showCategory = $this->getTotalProductCount($category) > 0;
            $transport = new \Magento\Framework\DataObject(
                [
                    'show_category' => $showCategory
                ]
            );

            $this->eventManager->dispatch(
                'codex_hide_empty_categories_before',
                [
                    'collection' => $categoryCollection,
                    'category' => $category,
                    'transport' => $transport
                ]
            );

            if ($transport->getShowCategory()) {
                $filteredCategoryCollection->addItem($category);
            }
            
            $mainWebsiteTopMenuCategory = array("Contact us / Request quote","Online Designer Help Videos");
            if(in_array($category->getName(), $mainWebsiteTopMenuCategory) && $storeCode == 'default'){
                $filteredCategoryCollection->addItem($category);
            }
        }
    }

    protected function getTotalProductCount(\Magento\Catalog\Model\Category $category)
    {
        return $category->getProductCollection()->getSize();
    }
}