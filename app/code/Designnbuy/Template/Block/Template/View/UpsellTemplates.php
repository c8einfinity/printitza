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
class UpsellTemplates extends \Designnbuy\Template\Block\Template\TemplateList\AbstractList
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

        $this->_templateCollection = $this->getCurrentTemplate()->getUpsellTemplates()->addAttributeToSelect('title')->addAttributeToSelect('identifier')
            ->addActiveFilter();
            //->setPageSize($pageSize ?: 5);

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
     * @return \Designnbuy\Template\Model\Template
     */
    public function getCurrentTemplate()
    {

        return $this->_coreRegistry->registry('current_designer_design_view');
    }

}
