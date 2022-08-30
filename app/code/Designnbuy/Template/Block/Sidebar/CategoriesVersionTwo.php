<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Template\Block\Sidebar;

use Magento\Store\Model\ScopeInterface;

/**
 * Template sidebar categories block
 */
class CategoriesVersionTwo extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Designnbuy\Template\Model\ResourceModel\Category\Collection
     */
    protected $_categoryCollection;

    /**
     * @var \Designnbuy\Template\Model\ResourceModel\Template\CollectionFactory
     */
    protected $_templateCollectionFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;
    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Designnbuy\Template\Model\ResourceModel\Category\Collection $categoryCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Designnbuy\Template\Model\ResourceModel\Category\Collection $categoryCollection,
        \Designnbuy\Template\Model\ResourceModel\Template\CollectionFactory $templateCollectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_categoryCollection = $categoryCollection;
        $this->_templateCollectionFactory = $templateCollectionFactory;
        $this->_coreRegistry = $coreRegistry;
    }

    /**
     * Get grouped categories
     * @return \Designnbuy\Template\Model\ResourceModel\Category\Collection
     */
    public function getTemplateCategoriesCollection()
    {
            $catIds = $this->getCategoriesAvailableInProducts();
            
            $templateCategories = $this->_categoryCollection->addAttributeToSelect('*')
                ->addAttributeToFilter('status', 1)
                ->addAttributeToFilter(
                    'entity_id',
                    ['in' => $catIds]
                )
                //->addStoreFilter($this->_storeManager->getStore()->getId())
                ->setOrder('position');

            
            foreach ($templateCategories as $key => $item) {
                
                
                $maxDepth = $this->maxDepth();
                if ($maxDepth > 0 && $item->getLevel() >= $maxDepth) {
                    unset($templateCategories[$key]);
                }
            }
            
        return $templateCategories;
    }

    public function getCategoriesAvailableInProducts()
    {
        $this->_templateCollection = $this->_templateCollectionFactory->create()
            ->addAttributeToSelect('category_id')
            ->addActiveFilter()
            //->addProductFilter($product)
            ->addTemplateFilter()
            ->addWebSiteFilter($this->_storeManager->getWebsite()->getId(), false)
            ->setStoreId($this->_storeManager->getStore()->getId());

        $product = $this->_coreRegistry->registry('product');
        if($product != null){
            $this->_templateCollection->addProductFilter($product);
        }
        $catIds = [];
        foreach($this->_templateCollection as $dt){
            $catId = explode(",",$dt->getCategoryId());
            
            if(count($catId) > 1){

            }
            
            $catIds = array_merge($catIds,$catId);
                        
        }
        if(!empty($catIds)){
            return array_unique($catIds);
        }
        return [];
    }

    /**
     * Retrieve categories maximum depth
     * @return int
     */
    public function maxDepth()
    {
        $maxDepth = 3;
        return (int)$maxDepth;
    }
}
