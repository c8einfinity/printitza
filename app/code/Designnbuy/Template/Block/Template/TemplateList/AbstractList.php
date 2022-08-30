<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Template\Block\Template\TemplateList;

use Magento\Store\Model\ScopeInterface;

/**
 * Abstract template template list block
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
    protected $_template;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Designnbuy\Template\Model\ResourceModel\Template\CollectionFactory
     */
    protected $_templateCollectionFactory;

    /**
     * @var \Designnbuy\Template\Model\ResourceModel\Template\Collection
     */
    protected $_templateCollection;

    /**
     * @var \Designnbuy\Template\Model\Url
     */
    protected $_url;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Designnbuy\Template\Model\ResourceModel\Template\CollectionFactory $templateCollectionFactory
     * @param \Designnbuy\Template\Model\Url $url
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Designnbuy\Template\Model\ResourceModel\Template\CollectionFactory $templateCollectionFactory,
        \Designnbuy\Template\Model\Url $url,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_coreRegistry = $coreRegistry;
        $this->_filterProvider = $filterProvider;
        $this->_templateCollectionFactory = $templateCollectionFactory;
        $this->_url = $url;
    }

    /**
     * Prepare templates collection
     *
     * @return void
     */
    protected function _prepareTemplateCollection()
    {
        
        $this->_templateCollection = $this->_templateCollectionFactory->create()
            ->addAttributeToSelect('*')
            ->addActiveFilter()
            //->addProductFilter($product)
            ->addTemplateFilter()
            ->addWebSiteFilter($this->_storeManager->getWebsite()->getId(), false)
            ->setStoreId($this->_storeManager->getStore()->getId());
        
        /************** If designer's created template then only *****************/

        /* $this->_templateCollection->addAttributeToFilter(array(
            array(
                'attribute' => 'marketplace_status',
                'eq' => '3'),
            array(
                'attribute' => 'marketplace_status',
                'eq' => null)
            )); */

        /************** End If designer's created template then only *****************/

        $product = $this->_coreRegistry->registry('product');
        if($product != null){
            $this->_templateCollection->addProductFilter($product)->setOrder('related_product.position', 'ASC');
            $this->_templateCollection->getSelect()->order('related_product.position', 'ASC');
        }
        if($this->getRequest()->getFullActionName() == "template_category_view"){ 
            $this->_templateCollection->setOrder('entity_id','DESC');
        }
           // ->setOrder('related_product.position', 'ASC');
        

        if ($this->getPageSize()) {
            $this->_templateCollection->setPageSize($this->getPageSize());
        }

    }

    /**
     * Prepare templates collection
     *
     * @return \Designnbuy\Template\Model\ResourceModel\Template\Collection
     */
    public function getTemplateCollection()
    {
        if (is_null($this->_templateCollection)) {
            $this->_prepareTemplateCollection();
        }

        return $this->_templateCollection;
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        /*if (!$this->_scopeConfig->getValue(
            \Designnbuy\Template\Helper\Config::XML_PATH_EXTENSION_ENABLED,
            ScopeInterface::SCOPE_STORE
        )) {
            return '';
        }*/

        return parent::_toHtml();
    }
}
