<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Designidea\Block\Designidea\View;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\View\Element\AbstractBlock;

/**
 * Designidea designidea related designideas block
 */
class RelatedDesignideas extends \Designnbuy\Designidea\Block\Designidea\DesignideaList\AbstractList
{
    /**
     * Prepare designideas collection
     *
     * @return void
     */
    protected function _prepareDesignideaCollection()
    {
        $pageSize = (int) $this->_scopeConfig->getValue(
            'dnbdesignidea/designidea_view/related_designideas/number_of_designideas',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $this->_designideaCollection = $this->getDesignidea()->getRelatedDesignideas()
            ->addActiveFilter()
            ->setPageSize($pageSize ?: 5);

        $this->_designideaCollection->getSelect()->order('rl.position', 'ASC');
    }

    /**
     * Retrieve true if Display Related Designideas enabled
     * @return boolean
     */
    public function displayDesignideas()
    {
        return (bool) $this->_scopeConfig->getValue(
            'dnbdesignidea/designidea_view/related_designideas/enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve designideas instance
     *
     * @return \Designnbuy\Designidea\Model\Category
     */
    public function getDesignidea()
    {
        if (!$this->hasData('designidea')) {
            $this->setData('designidea',
                $this->_coreRegistry->registry('current_designidea_designidea')
            );
        }
        return $this->getData('designidea');
    }

    /**
     * Get Block Identities
     * @return Array
     */
    public function getIdentities()
    {
        return [\Magento\Cms\Model\Page::CACHE_TAG . '_relateddesignideas_'.$this->getDesignidea()->getId()  ];
    }
}
