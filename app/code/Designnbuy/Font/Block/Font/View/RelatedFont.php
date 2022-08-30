<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Font\Block\Font\View;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\View\Element\AbstractBlock;

/**
 * Font font related fonts block
 */
class RelatedFonts extends \Designnbuy\Font\Block\Font\FontList\AbstractList
{
    /**
     * Prepare fonts collection
     *
     * @return void
     */
    protected function _prepareFontCollection()
    {
        $pageSize = (int) $this->_scopeConfig->getValue(
            'dnbfont/font_view/related_fonts/number_of_fonts',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $this->_fontCollection = $this->getFont()->getRelatedFonts()
            ->addActiveFilter()
            ->setPageSize($pageSize ?: 5);

        $this->_fontCollection->getSelect()->order('rl.position', 'ASC');
    }

    /**
     * Retrieve true if Display Related Fonts enabled
     * @return boolean
     */
    public function displayFonts()
    {
        return (bool) $this->_scopeConfig->getValue(
            'dnbfont/font_view/related_fonts/enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve fonts instance
     *
     * @return \Designnbuy\Font\Model\Category
     */
    public function getFont()
    {
        if (!$this->hasData('font')) {
            $this->setData('font',
                $this->_coreRegistry->registry('current_font_font')
            );
        }
        return $this->getData('font');
    }

    /**
     * Get Block Identities
     * @return Array
     */
    public function getIdentities()
    {
        return [\Magento\Cms\Model\Page::CACHE_TAG . '_relatedfonts_'.$this->getFont()->getId()  ];
    }
}
