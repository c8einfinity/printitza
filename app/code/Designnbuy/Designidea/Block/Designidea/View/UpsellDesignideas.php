<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Designidea\Block\Designidea\View;


use Magento\Framework\View\Element\AbstractBlock;

/**
 * Template template related templates block
 */
class UpsellDesignideas extends \Designnbuy\Designidea\Block\Designidea\DesignideaList\AbstractList
{
    /**
     * Prepare templates collection
     *
     * @return void
     */
    protected function _prepareDesignideaCollection()
    {
        $pageSize = (int) $this->_scopeConfig->getValue(
            'dnbtemplate/template_view/related_templates/number_of_templates',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        
        $this->_designideaCollection = $this->getCurrentDesignidea()->getUpsellDesignideas()->addAttributeToSelect('title')->addAttributeToSelect('identifier')
            ->addActiveFilter();

        $this->_designideaCollection->getSelect()->order('rl.position', 'ASC');
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
    public function getCurrentDesignidea()
    {

        return $this->_coreRegistry->registry('current_designer_design_view');
    }

}
