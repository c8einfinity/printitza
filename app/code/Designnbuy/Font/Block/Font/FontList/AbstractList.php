<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Font\Block\Font\FontList;

use Magento\Store\Model\ScopeInterface;

/**
 * Abstract font font list block
 */
abstract class AbstractList extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;

    /**
     * @var \Magento\Cms\Model\Page
     */
    protected $_font;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Designnbuy\Font\Model\ResourceModel\Font\CollectionFactory
     */
    protected $_fontCollectionFactory;

    /**
     * @var \Designnbuy\Font\Model\ResourceModel\Font\Collection
     */
    protected $_fontCollection;

    /**
     * @var \Designnbuy\Font\Model\Url
     */
    protected $_url;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Designnbuy\Font\Model\ResourceModel\Font\CollectionFactory $fontCollectionFactory
     * @param \Designnbuy\Font\Model\Url $url
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Designnbuy\Font\Model\ResourceModel\Font\CollectionFactory $fontCollectionFactory,
        \Designnbuy\Font\Model\Url $url,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_coreRegistry = $coreRegistry;
        $this->_filterProvider = $filterProvider;
        $this->_fontCollectionFactory = $fontCollectionFactory;
        $this->_url = $url;
    }

    /**
     * Prepare fonts collection
     *
     * @return void
     */
    protected function _prepareFontCollection()
    {
        $this->_fontCollection = $this->_fontCollectionFactory->create()
            ->addActiveFilter()
            ->addStoreFilter($this->_storeManager->getStore()->getId())
            ->setOrder('position', 'DESC');

        if ($this->getPageSize()) {
            $this->_fontCollection->setPageSize($this->getPageSize());
        }
    }

    /**
     * Prepare fonts collection
     *
     * @return \Designnbuy\Font\Model\ResourceModel\Font\Collection
     */
    public function getFontCollection()
    {
        if (is_null($this->_fontCollection)) {
            $this->_prepareFontCollection();
        }

        return $this->_fontCollection;
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->_scopeConfig->getValue(
            \Designnbuy\Font\Helper\Config::XML_PATH_EXTENSION_ENABLED,
            ScopeInterface::SCOPE_STORE
        )) {
            return '';
        }

        return parent::_toHtml();
    }
}
