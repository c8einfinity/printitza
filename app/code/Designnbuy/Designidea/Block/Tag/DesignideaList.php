<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Designidea\Block\Tag;

use Magento\Store\Model\ScopeInterface;

/**
 * Designidea tag designideas list
 */
class DesignideaList extends \Designnbuy\Designidea\Block\Designidea\DesignideaList
{
    /**
     * Prepare designideas collection
     *
     * @return void
     */
    protected function _prepareDesignideaCollection()
    {
        parent::_prepareDesignideaCollection();
        if ($tag = $this->getTag()) {
            $this->_designideaCollection->addTagFilter($tag);
        }
    }

    /**
     * Retrieve tag instance
     *
     * @return \Designnbuy\Designidea\Model\Tag
     */
    public function getTag()
    {
        return $this->_coreRegistry->registry('current_designidea_tag');
    }

    /**
     * Preparing global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        if ($tag = $this->getTag()) {
            $this->_addBreadcrumbs($tag->getTitle(), 'designidea_tag');
            $this->pageConfig->addBodyClass('designidea-tag-' . $tag->getIdentifier());
            $this->pageConfig->getTitle()->set($tag->getTitle());
            $this->pageConfig->addRemotePageAsset(
                $tag->getTagUrl(),
                'canonical',
                ['attributes' => ['rel' => 'canonical']]
            );
        }

        return parent::_prepareLayout();
    }

}
