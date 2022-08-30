<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Designidea\Block\Designidea\DesignideaList;

use Magento\Store\Model\ScopeInterface;

/**
 * Abstract designidea designidea list block
 */
abstract class AbstractList extends \Magento\Framework\View\Element\Template
{
    const STATUS_APPROVED = 3; 
    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $_filterProvider;

    /**
     * @var \Magento\Cms\Model\Page
     */
    protected $_designidea;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Designnbuy\Designidea\Model\ResourceModel\Designidea\CollectionFactory
     */
    protected $_designideaCollectionFactory;

    /**
     * @var \Designnbuy\Designidea\Model\ResourceModel\Designidea\Collection
     */
    protected $_designideaCollection;

    /**
     * @var \Designnbuy\Designidea\Model\Url
     */
    protected $_url;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Designnbuy\Designidea\Model\ResourceModel\Designidea\CollectionFactory $designideaCollectionFactory
     * @param \Designnbuy\Designidea\Model\Url $url
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Designnbuy\Designidea\Model\ResourceModel\Designidea\CollectionFactory $designideaCollectionFactory,
        \Designnbuy\Designidea\Model\Url $url,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_coreRegistry = $coreRegistry;
        $this->_filterProvider = $filterProvider;
        $this->_designideaCollectionFactory = $designideaCollectionFactory;
        $this->_url = $url;
    }

    /**
     * Prepare designideas collection
     *
     * @return void
     */
    protected function _prepareDesignideaCollection()
    {
        $this->_designideaCollection = $this->_designideaCollectionFactory->create()
            ->addAttributeToSelect('*')
            ->addActiveFilter()
            ->setStoreId($this->_storeManager->getStore()->getId())
            ->setOrder('publish_time', 'DESC');

        /************** If designer's created template then only *****************/

        /* $this->_designideaCollection->addAttributeToFilter(array(
                array(
                    'attribute' => 'marketplace_status',
                    'eq' => '3'),
                array(
                    'attribute' => 'marketplace_status',
                    'eq' => null)
                )); */

        /************** End If designer's created template then only *****************/

        //$this->_designideaCollection->addFieldToFilter('marketplace_status', self::STATUS_APPROVED);
        if ($this->getPageSize()) {
            $this->_designideaCollection->setPageSize($this->getPageSize());
        }
    }

    /**
     * Prepare designideas collection
     *
     * @return \Designnbuy\Designidea\Model\ResourceModel\Designidea\Collection
     */
    public function getDesignideaCollection()
    {
        if (is_null($this->_designideaCollection)) {
            $this->_prepareDesignideaCollection();
        }

        return $this->_designideaCollection;
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        /*if (!$this->_scopeConfig->getValue(
            \Designnbuy\Designidea\Helper\Config::XML_PATH_EXTENSION_ENABLED,
            ScopeInterface::SCOPE_STORE
        )) {
            return '';
        }*/

        return parent::_toHtml();
    }
}
