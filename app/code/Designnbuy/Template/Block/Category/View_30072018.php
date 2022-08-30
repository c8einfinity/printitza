<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Template\Block\Category;

use Magento\Store\Model\ScopeInterface;

/**
 * Template category view
 */
class View extends \Designnbuy\Template\Block\Template\TemplateList
{
    /**
     * @var \Designnbuy\Template\Model\ResourceModel\Template\Collection
     */
    protected $_templateCollection;


    /**
     * @var \Designnbuy\Template\Model\ResourceModel\Template\CollectionFactory
     */
    protected $_templateCollectionFactory;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Designnbuy\Template\Model\ResourceModel\Template\CollectionFactory $templateCollectionFactory,
        \Designnbuy\Template\Model\Url $url,
        array $data = []
    ) {
        parent::__construct($context, $coreRegistry, $filterProvider, $templateCollectionFactory, $url, $data);
    }

    /**
     * Preparing global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $category = $this->getCategory();
        if ($category) {
            $this->_addBreadcrumbs($category);
            $this->pageConfig->addBodyClass('template-category-' . $category->getIdentifier());
            $this->pageConfig->getTitle()->set($category->getMetaTitle());
            $this->pageConfig->setKeywords($category->getMetaKeywords());
            $this->pageConfig->setDescription($category->getMetaDescription());
            $this->pageConfig->addRemotePageAsset(
                $category->getCategoryUrl(),
                'canonical',
                ['attributes' => ['rel' => 'canonical']]
            );
            $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
            if ($pageMainTitle) {
                $pageMainTitle->setPageTitle(
                    $this->escapeHtml($category->getTitle())
                );
            }
        }
        if ($this->getTemplateCollection()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'designer.designidea.pager'
            )->setAvailableLimit(array(10=>10,20=>20,30=>30))->setShowPerPage(true)->setCollection(
                $this->getTemplateCollection()
            );
            $this->setChild('pager', $pager);
            $this->getTemplateCollection()->load();
        }
        return $this;
        //parent::_prepareLayout();
    }

    protected function prepareTemplateCollection()
    {
        $page = ($this->getRequest()->getParam('p'))? $this->getRequest()->getParam('p') : 1;
        $pageSize = ($this->getRequest()->getParam('limit'))? $this->getRequest()->getParam('limit') : 1;

        $this->_templateCollection = $this->_templateCollectionFactory->create()
            ->addAttributeToSelect('*')
            ->addActiveFilter()
            ->addTemplateFilter()
            ->addWebSiteFilter($this->_storeManager->getWebsite()->getId(), false)
            ->setStoreId($this->_storeManager->getStore()->getId());

        if ($category = $this->getCategory()) {
            $this->_templateCollection->addCategoryFilter($category->getId());
        }
        $this->_templateCollection->setPageSize($pageSize);
        $this->_templateCollection->setCurPage($page);
    }

    public function getTemplateCollection()
    {
        if (is_null($this->_templateCollection))
        {
            $this->prepareTemplateCollection();
        }
        return $this->_templateCollection;
    }

    public function getTemplateHtml($template)
    {
        return $this->getChildBlock('template.templates.list.item')->setTemplate($template)->toHtml();
    }

    /**
     * Retrieve category instance
     *
     * @return \Designnbuy\Template\Model\Category
     */
    public function getCategory()
    {
        return $this->_coreRegistry->registry('current_template_category');
    }

    /**
     * Prepare breadcrumbs
     *
     * @param  string $title
     * @param  string $key
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    protected function _addBreadcrumbs($title = null, $key = null)
    {
        if ($breadcrumbsBlock = $this->getBreadcrumbsBlock()) {
            $category = $this->getCategory();
            $parentCategories = [];
            while ($parentCategory = $category->getParentCategory()) {
                $parentCategories[] = $category = $parentCategory;
            }

            for ($i = count($parentCategories) - 1; $i >= 0; $i--) {
                $category = $parentCategories[$i];
                $breadcrumbsBlock->addCrumb('template_parent_category_' . $category->getId(), [
                    'label' => $category->getTitle(),
                    'title' => $category->getTitle(),
                    'link'  => $category->getCategoryUrl()
                ]);
            }

            $breadcrumbsBlock->addCrumb('template_category',[
                'label' => $category->getTitle(),
                'title' => $category->getTitle()
            ]);
        }
        parent::_addBreadcrumbs();
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
}
