<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Color\Block\Widget;

/**
 * Color recent colors widget
 */
class Recent extends \Designnbuy\Color\Block\Color\ColorList\AbstractList implements \Magento\Widget\Block\BlockInterface
{

    /**
     * @var \Designnbuy\Color\Model\CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * @var \Designnbuy\Color\Model\Category
     */
    protected $_category;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Designnbuy\Color\Model\ResourceModel\Color\CollectionFactory $colorCollectionFactory
     * @param \Designnbuy\Color\Model\Url $url
     * @param \Designnbuy\Color\Model\CategoryFactory $categoryFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Designnbuy\Color\Model\ResourceModel\Color\CollectionFactory $colorCollectionFactory,
        \Designnbuy\Color\Model\Url $url,
        \Designnbuy\Color\Model\CategoryFactory $categoryFactory,
        array $data = []
    ) {
        parent::__construct($context, $coreRegistry, $filterProvider, $colorCollectionFactory, $url, $data);
        $this->_categoryFactory = $categoryFactory;
    }

    /**
     * Set color template
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
        return $this->getData('title') ?: __('Recent Color Colors');
    }

    /**
     * Prepare colors collection
     *
     * @return void
     */
    protected function _prepareColorCollection()
    {
        $size = $this->getData('number_of_colors');
        if (!$size) {
            $size = (int) $this->_scopeConfig->getValue(
                'dnbcolor/sidebar/recent_colors/colors_per_page',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }

        $this->setPageSize($size);

        parent::_prepareColorCollection();

        if ($category = $this->getCategory()) {
            $categories = $category->getChildrenIds();
            $categories[] = $category->getId();
            $this->_colorCollection->addCategoryFilter($categories);
        }
    }

    /**
     * Retrieve category instance
     *
     * @return \Designnbuy\Color\Model\Category
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
     * Retrieve color short content
     * @param  \Designnbuy\Color\Model\Color $color
     *
     * @return string
     */
    public function getShorContent($color)
    {
        return $color->getShortFilteredContent();
    }
}
