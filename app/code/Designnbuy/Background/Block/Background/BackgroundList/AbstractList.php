<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Background\Block\Background\BackgroundList;

use Magento\Store\Model\ScopeInterface;

/**
 * Abstract background background list block
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
    protected $_background;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Designnbuy\Background\Model\ResourceModel\Background\CollectionFactory
     */
    protected $_backgroundCollectionFactory;

    /**
     * @var \Designnbuy\Background\Model\ResourceModel\Background\Collection
     */
    protected $_backgroundCollection;

    /**
     * @var \Designnbuy\Background\Model\Url
     */
    protected $_url;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Designnbuy\Background\Model\ResourceModel\Background\CollectionFactory $backgroundCollectionFactory
     * @param \Designnbuy\Background\Model\Url $url
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Designnbuy\Background\Model\ResourceModel\Background\CollectionFactory $backgroundCollectionFactory,
        \Designnbuy\Background\Model\Url $url,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_coreRegistry = $coreRegistry;
        $this->_filterProvider = $filterProvider;
        $this->_backgroundCollectionFactory = $backgroundCollectionFactory;
        $this->_url = $url;
    }

    /**
     * Prepare backgrounds collection
     *
     * @return void
     */
    protected function _prepareBackgroundCollection()
    {
        $this->_backgroundCollection = $this->_backgroundCollectionFactory->create()
            ->addActiveFilter()
            ->addStoreFilter($this->_storeManager->getStore()->getId())
            ->setOrder('creation_time', 'DESC');

        if ($this->getPageSize()) {
            $this->_backgroundCollection->setPageSize($this->getPageSize());
        }
    }

    /**
     * Prepare backgrounds collection
     *
     * @return \Designnbuy\Background\Model\ResourceModel\Background\Collection
     */
    public function getBackgroundCollection()
    {
        if (is_null($this->_backgroundCollection)) {
            $this->_prepareBackgroundCollection();
        }

        return $this->_backgroundCollection;
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->_scopeConfig->getValue(
            \Designnbuy\Background\Helper\Config::XML_PATH_EXTENSION_ENABLED,
            ScopeInterface::SCOPE_STORE
        )) {
            return '';
        }

        return parent::_toHtml();
    }
}
