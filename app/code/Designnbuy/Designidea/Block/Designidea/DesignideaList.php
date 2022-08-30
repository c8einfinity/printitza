<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Designidea\Block\Designidea;

use Magento\Store\Model\ScopeInterface;

/**
 * Designidea designidea list block
 */
class DesignideaList extends \Designnbuy\Designidea\Block\Designidea\DesignideaList\AbstractList
{
    /**
     * Block template file
     * @var string
     */
    protected $_defaultToolbarBlock = 'Designnbuy\Designidea\Block\Designidea\DesignideaList\Toolbar';

    /**
     * Preparing global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $page = $this->_request->getParam(
            \Designnbuy\Designidea\Block\Designidea\DesignideaList\Toolbar::PAGE_PARM_NAME
        );

        if ($page > 1) {
            $this->pageConfig->setRobots('NOINDEX,FOLLOW');
        }

        return parent::_prepareLayout();
    }

    /**
     * Retrieve designidea html
     * @param  \Designnbuy\Designidea\Model\Designidea $designidea
     * @return string
     */
    public function getDesignideaHtml($designidea)
    {
        return $this->getChildBlock('designidea.designideas.list.item')->setDesignidea($designidea)->toHtml();
    }

    /**
     * Retrieve Toolbar Block
     * @return \Designnbuy\Designidea\Block\Designidea\DesignideaList\Toolbar
     */
    public function getToolbarBlock()
    {
        $blockName = $this->getToolbarBlockName();

        if ($blockName) {
            $block = $this->getLayout()->getBlock($blockName);
            if ($block) {
                return $block;
            }
        }
        $block = $this->getLayout()->createBlock($this->_defaultToolbarBlock, uniqid(microtime()));
        return $block;
    }

    /**
     * Retrieve Toolbar Html
     * @return string
     */
    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }

    /**
     * Before block to html
     *
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $toolbar = $this->getToolbarBlock();

        // called prepare sortable parameters
        $collection = $this->getDesignideaCollection();

        // set collection to toolbar and apply sort
        $toolbar->setCollection($collection);

        $this->setChild('toolbar', $toolbar);
        //$toolbar->setAvailableLimit([10=>10,20=>20,50=>50,100=>100])->setShowPerPage(true);
        return parent::_beforeToHtml();
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
            $breadcrumbsBlock->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link' => $this->_storeManager->getStore()->getBaseUrl()
                ]
            );

            $designideaTitle = $this->_scopeConfig->getValue(
                'dnbdesignidea/index_page/title',
                ScopeInterface::SCOPE_STORE
            );
            $breadcrumbsBlock->addCrumb(
                'designidea',
                [
                    'label' => __($designideaTitle),
                    'title' => __($designideaTitle),
                    'link' => $this->_url->getBaseUrl()
                ]
            );

            if ($title) {
                $breadcrumbsBlock->addCrumb($key ?: 'designidea_item', ['label' => $title, 'title' => $title]);
            }
        }
    }

    /**
     * Retrieve breadcrumbs block
     *
     * @return mixed
     */
    protected function getBreadcrumbsBlock()
    {
        if ($this->_scopeConfig->getValue('web/default/show_cms_breadcrumbs', ScopeInterface::SCOPE_STORE)) {
            return $this->getLayout()->getBlock('breadcrumbs');
        }

        return false;
    }

}
