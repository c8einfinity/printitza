<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Color\Block\Color;

use Magento\Store\Model\ScopeInterface;

/**
 * Color color list block
 */
class ColorList extends \Designnbuy\Color\Block\Color\ColorList\AbstractList
{
    /**
     * Block template file
     * @var string
     */
    protected $_defaultToolbarBlock = 'Designnbuy\Color\Block\Color\ColorList\Toolbar';

    /**
     * Preparing global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $page = $this->_request->getParam(
            \Designnbuy\Color\Block\Color\ColorList\Toolbar::PAGE_PARM_NAME
        );

        if ($page > 1) {
            $this->pageConfig->setRobots('NOINDEX,FOLLOW');
        }

        return parent::_prepareLayout();
    }

    /**
     * Retrieve color html
     * @param  \Designnbuy\Color\Model\Color $color
     * @return string
     */
    public function getColorHtml($color)
    {
        return $this->getChildBlock('color.colors.list.item')->setColor($color)->toHtml();
    }

    /**
     * Retrieve Toolbar Block
     * @return \Designnbuy\Color\Block\Color\ColorList\Toolbar
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
        $collection = $this->getColorCollection();

        // set collection to toolbar and apply sort
        $toolbar->setCollection($collection);
        $this->setChild('toolbar', $toolbar);

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

            $colorTitle = $this->_scopeConfig->getValue(
                'dnbcolor/index_page/title',
                ScopeInterface::SCOPE_STORE
            );
            $breadcrumbsBlock->addCrumb(
                'color',
                [
                    'label' => __($colorTitle),
                    'title' => __($colorTitle),
                    'link' => $this->_url->getBaseUrl()
                ]
            );

            if ($title) {
                $breadcrumbsBlock->addCrumb($key ?: 'color_item', ['label' => $title, 'title' => $title]);
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
