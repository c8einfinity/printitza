<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Font\Block\Search;

use Magento\Store\Model\ScopeInterface;

/**
 * Font search result block
 */
class FontList extends \Designnbuy\Font\Block\Font\FontList
{
	/**
	 * Retrieve query
	 * @return string
	 */
    public function getQuery()
    {
        return urldecode($this->getRequest()->getParam('q'));
    }

    /**
     * Prepare fonts collection
     *
     * @return void
     */
    protected function _prepareFontCollection()
    {
        parent::_prepareFontCollection();
        $this->_fontCollection->addSearchFilter(
            $this->getQuery()
        );
    }

    /**
     * Preparing global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $title = $this->_getTitle();
        $this->_addBreadcrumbs($title, 'font_search');
        $this->pageConfig->getTitle()->set($title);

        return parent::_prepareLayout();
    }

    /**
     * Retrieve title
     * @return string
     */
    protected function _getTitle()
    {
        return __('Search "%1"', $this->getQuery());
    }

}
