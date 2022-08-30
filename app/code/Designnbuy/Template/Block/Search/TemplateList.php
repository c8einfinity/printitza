<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Template\Block\Search;

use Magento\Store\Model\ScopeInterface;

/**
 * Template search result block
 */
class TemplateList extends \Designnbuy\Template\Block\Template\TemplateList
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
	 * Retrieve query
	 * @return string
	 */
    public function getQuery()
    {
        return urldecode($this->getRequest()->getParam('q'));
    }

    /**
     * Prepare templates collection
     *
     * @return void
     */
    /*protected function _prepareTemplateCollection()
    {
        parent::_prepareTemplateCollection();
        $this->_templateCollection->addSearchFilter(
            $this->getQuery()
        );
    }*/

    /**
     * Preparing global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $title = $this->_getTitle();
        $this->_addBreadcrumbs($title, 'template_search');
        $this->pageConfig->getTitle()->set($title);
        //return parent::_prepareLayout();

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
    }

    /**
     * Retrieve title
     * @return string
     */
    protected function _getTitle()
    {
        return __('Search "%1"', $this->getQuery());
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
        
        if ($category = $this->getCategory()) {
            $this->_templateCollection->addCategoryFilter($category->getId());
        }

        $this->_templateCollection->addSearchFilter($this->getQuery());
        
        $this->_templateCollection->setPageSize($pageSize);
        $this->_templateCollection->setCurPage($page);
    }

    public function getTemplateCollection()
    {
        if (is_null($this->_templateCollection)) {
            $this->prepareTemplateCollection();
        }
        return $this->_templateCollection;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
}
