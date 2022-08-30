<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Background\Block\Background\View;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\View\Element\AbstractBlock;

/**
 * Background background related backgrounds block
 */
class RelatedBackgrounds extends \Designnbuy\Background\Block\Background\BackgroundList\AbstractList
{
    /**
     * Prepare backgrounds collection
     *
     * @return void
     */
    protected function _prepareBackgroundCollection()
    {
        $pageSize = (int) $this->_scopeConfig->getValue(
            'dnbbackground/background_view/related_backgrounds/number_of_backgrounds',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $this->_backgroundCollection = $this->getBackground()->getRelatedBackgrounds()
            ->addActiveFilter()
            ->setPageSize($pageSize ?: 5);

        $this->_backgroundCollection->getSelect()->order('rl.position', 'ASC');
    }

    /**
     * Retrieve true if Display Related Backgrounds enabled
     * @return boolean
     */
    public function displayBackgrounds()
    {
        return (bool) $this->_scopeConfig->getValue(
            'dnbbackground/background_view/related_backgrounds/enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve backgrounds instance
     *
     * @return \Designnbuy\Background\Model\Category
     */
    public function getBackground()
    {
        if (!$this->hasData('background')) {
            $this->setData('background',
                $this->_coreRegistry->registry('current_background_background')
            );
        }
        return $this->getData('background');
    }

    /**
     * Get Block Identities
     * @return Array
     */
    public function getIdentities()
    {
        return [\Magento\Cms\Model\Page::CACHE_TAG . '_relatedbackgrounds_'.$this->getBackground()->getId()  ];
    }
}
