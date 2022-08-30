<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Color\Block\Tag;

use Magento\Store\Model\ScopeInterface;

/**
 * Color tag colors list
 */
class ColorList extends \Designnbuy\Color\Block\Color\ColorList
{
    /**
     * Prepare colors collection
     *
     * @return void
     */
    protected function _prepareColorCollection()
    {
        parent::_prepareColorCollection();
        if ($tag = $this->getTag()) {
            $this->_colorCollection->addTagFilter($tag);
        }
    }

    /**
     * Retrieve tag instance
     *
     * @return \Designnbuy\Color\Model\Tag
     */
    public function getTag()
    {
        return $this->_coreRegistry->registry('current_color_tag');
    }

    /**
     * Preparing global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        if ($tag = $this->getTag()) {
            $this->_addBreadcrumbs($tag->getTitle(), 'color_tag');
            $this->pageConfig->addBodyClass('color-tag-' . $tag->getIdentifier());
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
