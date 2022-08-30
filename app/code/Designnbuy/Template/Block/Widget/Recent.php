<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Template\Block\Widget;

/**
 * Template recent templates widget
 */
class Recent extends \Designnbuy\Template\Block\Template\TemplateList\AbstractList implements \Magento\Widget\Block\BlockInterface
{

    /**
     * @var \Designnbuy\Template\Model\CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * @var \Designnbuy\Template\Model\Category
     */
    protected $_category;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Designnbuy\Template\Model\ResourceModel\Template\CollectionFactory $templateCollectionFactory
     * @param \Designnbuy\Template\Model\Url $url
     * @param \Designnbuy\Template\Model\CategoryFactory $categoryFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Designnbuy\Template\Model\ResourceModel\Template\CollectionFactory $templateCollectionFactory,
        \Designnbuy\Template\Model\Url $url,
        \Designnbuy\Template\Model\CategoryFactory $categoryFactory,
        array $data = []
    ) {
        parent::__construct($context, $coreRegistry, $filterProvider, $templateCollectionFactory, $url, $data);
        $this->_categoryFactory = $categoryFactory;
    }

    /**
     * Set template template
     *
     * @return this
     */
    public function _toHtml()
    {
        $this->setTemplate(
            $this->getData('custom_template') ?: 'widget/recent.phtml'
        );

        return parent::_toHtml();
    }

    /**
     * Retrieve block title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->getData('title') ?: __('Recent Template Templates');
    }

    /**
     * Prepare templates collection
     *
     * @return void
     */
    protected function _prepareTemplateCollection()
    {
        $size = $this->getData('number_of_templates');
        if (!$size) {
            $size = (int) $this->_scopeConfig->getValue(
                'dnbtemplate/sidebar/recent_templates/templates_per_page',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }

        $this->setPageSize($size);

        parent::_prepareTemplateCollection();

        if ($category = $this->getCategory()) {
            $categories = $category->getChildrenIds();
            $categories[] = $category->getId();
            $this->_templateCollection->addCategoryFilter($categories);
        }
    }

    /**
     * Retrieve category instance
     *
     * @return \Designnbuy\Template\Model\Category
     */
    public function getCategory()
    {
        if ($this->_category === null) {
            if ($categoryId = $this->getData('category_id')) {
                $category = $this->_categoryFactory->create();
                $category->load($categoryId);

                $storeId = $this->_storeManager->getStore()->getId();
                if ($category->isVisibleOnStore($storeId)) {
                    $category->setStoreId($storeId);
                    return $this->_category = $category;
                }
            }

            $this->_category = false;
        }

        return $this->_category;
    }

    /**
     * Retrieve template short content
     * @param  \Designnbuy\Template\Model\Template $template
     *
     * @return string
     */
    public function getShorContent($template)
    {
        return $template->getShortFilteredContent();
    }
}
