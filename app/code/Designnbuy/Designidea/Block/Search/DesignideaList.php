<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Designidea\Block\Search;

use Magento\Store\Model\ScopeInterface;

/**
 * Designidea search result block
 */
class DesignideaList extends \Designnbuy\Designidea\Block\Designidea\DesignideaList
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
     * Prepare designideas collection
     *
     * @return void
     */
    protected function _prepareDesignideaCollection()
    {
        parent::_prepareDesignideaCollection();
        $this->_designideaCollection->addSearchFilter(
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
        $this->_addBreadcrumbs($title, 'designidea_search');
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
