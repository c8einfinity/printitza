<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Font\Block\Widget;

/**
 * Font recent fonts widget
 */
class Recent extends \Designnbuy\Font\Block\Font\FontList\AbstractList implements \Magento\Widget\Block\BlockInterface
{

    /**
     * @var \Designnbuy\Font\Model\CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * @var \Designnbuy\Font\Model\Category
     */
    protected $_category;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Designnbuy\Font\Model\ResourceModel\Font\CollectionFactory $fontCollectionFactory
     * @param \Designnbuy\Font\Model\Url $url
     * @param \Designnbuy\Font\Model\CategoryFactory $categoryFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Designnbuy\Font\Model\ResourceModel\Font\CollectionFactory $fontCollectionFactory,
        \Designnbuy\Font\Model\Url $url,
        \Designnbuy\Font\Model\CategoryFactory $categoryFactory,
        array $data = []
    ) {
        parent::__construct($context, $coreRegistry, $filterProvider, $fontCollectionFactory, $url, $data);
        $this->_categoryFactory = $categoryFactory;
    }

    /**
     * Set font template
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
        return $this->getData('title') ?: __('Recent Font Fonts');
    }

    /**
     * Prepare fonts collection
     *
     * @return void
     */
    protected function _prepareFontCollection()
    {
        $size = $this->getData('number_of_fonts');
        if (!$size) {
            $size = (int) $this->_scopeConfig->getValue(
                'dnbfont/sidebar/recent_fonts/fonts_per_page',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }

        $this->setPageSize($size);

        parent::_prepareFontCollection();

        if ($category = $this->getCategory()) {
            $categories = $category->getChildrenIds();
            $categories[] = $category->getId();
            $this->_fontCollection->addCategoryFilter($categories);
        }
    }

    /**
     * Retrieve category instance
     *
     * @return \Designnbuy\Font\Model\Category
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
     * Retrieve font short content
     * @param  \Designnbuy\Font\Model\Font $font
     *
     * @return string
     */
    public function getShorContent($font)
    {
        return $font->getShortFilteredContent();
    }
}
