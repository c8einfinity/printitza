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
class Categories extends \Magento\Framework\View\Element\Template
{
    use Widget;

    /**
     * @var string
     */
    protected $_widgetKey = 'categories';

    /**
     * @var \Designnbuy\Template\Model\ResourceModel\Category\Collection
     */
    protected $_categoryCollection;

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
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_categoryCollection = $categoryCollection;
        $this->_coreRegistry = $coreRegistry;
    }

    /**
     * Get grouped categories
     * @return \Designnbuy\Template\Model\ResourceModel\Category\Collection
     */
    public function getGroupedChilds()
    {
        $k = 'grouped_childs';
        
        if (!$this->hasData($k)) {

            $array = $this->_categoryCollection->addAttributeToSelect('*')
                ->addFieldToFilter('status', 1)
                ->addAttributeToSelect('path')
                //->addStoreFilter($this->_storeManager->getStore()->getId())
                ->setOrder('position')
                ->getTreeOrderedArray();
            /* foreach ($array as $key => $item) {
                
                if($this->getCurrentCategory()){
                    if($item->getPath()){
                        
                        if($item->getPath() != $this->getCurrentCategory()->getId()){
                            unset($array[$key]);
                        }
                    } else {
                        if($item->getId() != $this->getCurrentCategory()->getId()){
                            unset($array[$key]);
                        }
                    }
                }
                $maxDepth = $this->maxDepth();
                if ($maxDepth > 0 && $item->getLevel() >= $maxDepth) {
                    unset($array[$key]);
                }
            } */
            
            $this->setData($k, $array);
        }

        return $this->getData($k);
    }

    /**
     * Retrieve category instance
     *
     * @return \Designnbuy\Template\Model\Category
     */
    public function getCurrentCategory()
    {
        return $this->_coreRegistry->registry('current_template_category');
    }

    /**
     * Retrieve categories maximum depth
     * @return int
     */
    public function maxDepth()
    {
        /*$maxDepth = $this->_scopeConfig->getValue(
            'mfblog/sidebar/'.$this->_widgetKey.'/max_depth',
            ScopeInterface::SCOPE_STORE
        );*/
        $maxDepth = 3;
        return (int)$maxDepth;
    }

    /**
     * Retrieve true if need to show templates count
     * @return int
     */
    public function showTemplatesCount()
    {
        $key = 'show_templates_count';
        if (!$this->hasData($key)) {
            $this->setData($key, (bool)$this->_scopeConfig->getValue(
                'dnbtemplate/sidebar/'.$this->_widgetKey.'/show_templates_count', ScopeInterface::SCOPE_STORE
            ));
        }
        return $this->getData($key);
    }


    /**
     * Retrieve block identities
     * @return array
     */
    public function getIdentities()
    {
        return [\Magento\Cms\Model\Block::CACHE_TAG . '_template_categories_widget'  ];
    }

    /**
     * Get subcategories
     * @return \Magefan\Blog\Model\ResourceModel\Category\Collection
     */
    public function getSubCategories($category)
    {
        /*$subCategories = $this->_categoryCollection;
        $subCategories
            ->addActiveFilter()
           // ->addStoreFilter($this->_storeManager->getStore()->getId())
            ->setOrder('position')
            ->addFieldToFilter('entity_id', ['in' => $category->getChildrenIds(false)]);
        return $subCategories;*/
    }
}
