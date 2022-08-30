<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Designidea\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Data\Tree\Node;
use Magento\Store\Model\ScopeInterface;
use Designnbuy\Designidea\Helper\Config;

/**
 * Designidea observer
 */
class PageBlockHtmlTopmenuBethtmlBeforeObserver implements ObserverInterface
{
    /**
     * Show top menu item config path
     */
    const XML_PATH_TOP_MENU_SHOW_ITEM = 'dnbdesignidea/top_menu/show_item';

    /**
     * Top menu item text config path
     */
    const XML_PATH_TOP_MENU_ITEM_TEXT = 'dnbdesignidea/top_menu/item_text';

    /**
     * @var \Designnbuy\Designidea\Model\Url
     */
    protected $_url;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $_storeManager;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Catgegory Collection
     *
     * @var \Designnbuy\Designidea\Model\ResourceModel\Category\Collection
     */
    protected $_categoryCollection;


    public function __construct(
        \Designnbuy\Designidea\Model\Url $url,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry,
        \Designnbuy\Designidea\Model\ResourceModel\Category\Collection $categoryCollection,
        \Designnbuy\Designidea\Helper\Menu $menuHelper
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_url = $url;
        $this->_storeManager = $storeManager;
        $this->_coreRegistry = $registry;
        $this->_categoryCollection = $categoryCollection;
        $this->menuHelper = $menuHelper;
    }

    /**
     * Page block html topmenu gethtml before
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        return;
        /** @var \Magento\Framework\Data\Tree\Node $menu */
        $menu = $observer->getMenu();
        $tree = $menu->getTree();

        $categoryNode = $this->menuHelper->getCategoryNode($menu, $menu->getTree());
        if ($categoryNode) {
            $menu->addChild($categoryNode);
        }
        return;
        
    }

    /**
     * Convert category to array
     *
     * @param \Magento\Catalog\Model\Category $category
     * @param \Magento\Catalog\Model\Category $currentCategory
     * @return array
     */
    private function getCategoryAsArray($category, $currentCategory)
    {
        return [
            'name' => $category->getTitle(),
            'id' => 'category-node-' . $category->getId(),
            'url' => $category->getCategoryUrl(),
            //'has_active' => in_array((string)$category->getId(), explode('/', $currentCategory->getPath()), true),
            //'is_active' => $category->getId() == $currentCategory->getId()
        ];
    }

    /**
     * @return void
     */

    public function getCurrentCategory()
    {
        return $this->_coreRegistry->registry('current_category');
    }
}
