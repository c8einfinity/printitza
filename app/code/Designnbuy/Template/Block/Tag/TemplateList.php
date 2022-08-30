<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Template\Block\Tag;

use Magento\Store\Model\ScopeInterface;

/**
 * Template tag templates list
 */
class TemplateList extends \Designnbuy\Template\Block\Template\TemplateList
{
    /**
     * Prepare templates collection
     *
     * @return void
     */
    protected function _prepareTemplateCollection()
    {
        parent::_prepareTemplateCollection();
        if ($tag = $this->getTag()) {
            $this->_templateCollection->addTagFilter($tag);
        }
    }

    /**
     * Retrieve tag instance
     *
     * @return \Designnbuy\Template\Model\Tag
     */
    public function getTag()
    {
        return $this->_coreRegistry->registry('current_template_tag');
    }

    /**
     * Preparing global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        if ($tag = $this->getTag()) {
            $this->_addBreadcrumbs($tag->getTitle(), 'template_tag');
            $this->pageConfig->addBodyClass('template-tag-' . $tag->getIdentifier());
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
