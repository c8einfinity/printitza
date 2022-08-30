<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Color\Block\Color\View;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\View\Element\AbstractBlock;

/**
 * Color color related colors block
 */
class RelatedColors extends \Designnbuy\Color\Block\Color\ColorList\AbstractList
{
    /**
     * Prepare colors collection
     *
     * @return void
     */
    protected function _prepareColorCollection()
    {
        $pageSize = (int) $this->_scopeConfig->getValue(
            'dnbcolor/color_view/related_colors/number_of_colors',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $this->_colorCollection = $this->getColor()->getRelatedColors()
            ->addActiveFilter()
            ->setPageSize($pageSize ?: 5);

        $this->_colorCollection->getSelect()->order('rl.position', 'ASC');
    }

    /**
     * Retrieve true if Display Related Colors enabled
     * @return boolean
     */
    public function displayColors()
    {
        return (bool) $this->_scopeConfig->getValue(
            'dnbcolor/color_view/related_colors/enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve colors instance
     *
     * @return \Designnbuy\Color\Model\Category
     */
    public function getColor()
    {
        if (!$this->hasData('color')) {
            $this->setData('color',
                $this->_coreRegistry->registry('current_color_color')
            );
        }
        return $this->getData('color');
    }

    /**
     * Get Block Identities
     * @return Array
     */
    public function getIdentities()
    {
        return [\Magento\Cms\Model\Page::CACHE_TAG . '_relatedcolors_'.$this->getColor()->getId()  ];
    }
}
