<?php
/**
 * Copyright © 2015-2017 Ihor Vansach (ihor@magefan.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Designnbuy\Template\Helper;

use Magento\Framework\Data\Tree\Node;
use Magento\Store\Model\ScopeInterface;

/**
 * Magefan Blog Menu Helper
 * Example: {{block class="Magefan\Blog\Block\Smartwave\Megamenu\Topmenu" menu_type="fullwidth" }}
 */
class Menu extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magefan\Blog\Model\Url
     */
    protected $_url;

    /**
     * @param \Magento\Framework\Registry $registry,
     */
    protected $registry;

    /**
     * @var \Magefan\Blog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Magento\Framework\App\Helper\Context                        $context
     * @param \Magefan\Blog\Model\Url                                      $url
     * @param \Magento\Framework\Registry                                  $registry
     * @param \Magefan\Blog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \Magento\Store\Model\StoreManagerInterface                   $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Designnbuy\Template\Model\Url $url,
        \Magento\Framework\Registry $registry,
        \Designnbuy\Template\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->url = $url;
        $this->registry = $registry;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }


    /**
     * Retrieve blog menu nodes
     * @param  mixed $menu
     * @param  mixed $tree
     * @return \Magento\Framework\Data\Tree\Node | null
     */
    public function getCategoryNode($addedNodes, $menu = null, $tree = null)
    {
       /* if (!$this->scopeConfig->isSetFlag(Config::XML_PATH_TOP_MENU_SHOW_ITEM, ScopeInterface::SCOPE_STORE)) {
            return;
        }

        if (!$this->scopeConfig->isSetFlag(Config::XML_PATH_EXTENSION_ENABLED, ScopeInterface::SCOPE_STORE)) {
            return;
        }*/

        if (null == $tree) {
            $tree = new \Magento\Framework\Data\Tree;
        }
        if(empty($addedNodes)){
            $addedNodes = [];

            $data = [
                'name'      => 'Templates',
                'id'        => 'designnbuy-template-category',
                'url'       => $this->url->getBaseUrl(),
                'is_active' => ($this->_request->getModuleName() == 'template')
            ];

            $addedNodes[0] = new Node($data, 'id', $tree, $menu);
        }


        /*$includeCategories = $this->scopeConfig->getValue(
            Config::XML_PATH_TOP_MENU_INCLUDE_CATEGORIES,
            ScopeInterface::SCOPE_STORE
        );*/
        $includeCategories = 1;
        if ($includeCategories) {
            /*$maxDepth = $this->scopeConfig->getValue(
                Config::XML_PATH_TOP_MENU_MAX_DEPTH,
                ScopeInterface::SCOPE_STORE
            );*/
            $maxDepth = 0;
            $items = $this->getGroupedChilds();

            $currentCategoryId = $this->getCurrentCategory()
                ? $this->getCurrentCategory()->getId()
                : 0;

            foreach ($items as $item) {
                $parentId = (int) $item->getParentId();

                if (!isset($addedNodes[$parentId])) {
                    continue;
                }

                if ($maxDepth > 0 && $item->getLevel() >= $maxDepth) {
                    continue;
                }
                if($this->menuFilter($item)){
                    continue;
                }
                if($item->getTemplateIncludeInMenu() != "" && $item->getTemplateIncludeInMenu() != 1){
                    continue;
                }

                $data = [
                    'name'      => $item->getTitle(),
                    'id'        => 'designnbuy-tempalte-category-' . $item->getId(),
                    'url'       => $item->getCategoryUrl(),
                    'is_active' => $currentCategoryId == $item->getId()
                ];

                $addedNodes[$item->getId()] = new Node($data, 'id', $tree, $menu);
                $addedNodes[$parentId]->addChild(
                    $addedNodes[$item->getId()]
                );
            }
        }

        return $addedNodes[0];
    }

    /**
     * Retrieve sorted array of categories
     * @return array
     */
    protected function getGroupedChilds()
    {
        return $this->categoryCollectionFactory->create()
            ->addAttributeToSelect('title')
            ->addAttributeToSelect('path')
            ->addAttributeToSelect('template_include_in_menu')
            ->addAttributeToSelect('identifier')
            ->addActiveFilter()
            //->addStoreFilter($this->storeManager->getStore()->getId())
            //->addFieldToFilter('include_in_menu', 1)
            ->setOrder('title')
            ->getTreeOrderedArray();
    }

    /**
     * Retrieve current blog category
     * @return \Magefan\Blog\Model\Category | null
     */
    protected function getCurrentCategory()
    {
        return $this->registry->registry('current_template_category');
    }
    public function menuFilter($item){
        return false;
    }
}
