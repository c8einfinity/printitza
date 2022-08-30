<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Template\Block\Template\View;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\View\Element\AbstractBlock;

/**
 * Template template related templates block
 */
class RelatedTemplates extends \Designnbuy\Template\Block\Template\TemplateList\AbstractList
{
    /**
     * Prepare templates collection
     *
     * @return void
     */
    protected function _prepareTemplateCollection()
    {
        $pageSize = (int) $this->_scopeConfig->getValue(
            'dnbtemplate/template_view/related_templates/number_of_templates',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $this->_templateCollection = $this->getTemplate()->getRelatedTemplates()
            ->addActiveFilter()
            ->setPageSize($pageSize ?: 5);

        $this->_templateCollection->getSelect()->order('rl.position', 'ASC');
    }

    /**
     * Retrieve true if Display Related Templates enabled
     * @return boolean
     */
    public function displayTemplates()
    {
        return (bool) $this->_scopeConfig->getValue(
            'dnbtemplate/template_view/related_templates/enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve templates instance
     *
     * @return \Designnbuy\Template\Model\Category
     */
    public function getTemplate()
    {
        if (!$this->hasData('template')) {
            $this->setData('template',
                $this->_coreRegistry->registry('current_template_template')
            );
        }
        return $this->getData('template');
    }

    /**
     * Get Block Identities
     * @return Array
     */
    public function getIdentities()
    {
        return [\Magento\Cms\Model\Page::CACHE_TAG . '_relatedtemplates_'.$this->getTemplate()->getId()  ];
    }
}
