<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Background\Block\Catalog\Product;

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
            'dnbbackground/product_page/number_of_related_backgrounds',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if (!$pageSize) {
            $pageSize = 5;
        }
        $this->setPageSize($pageSize);

        parent::_prepareBackgroundCollection();

        $product = $this->getProduct();
        $this->_backgroundCollection->getSelect()->joinLeft(
            ['rl' => $product->getResource()->getTable('designnbuy_background_background_relatedproduct')],
            'main_table.background_id = rl.background_id',
            ['position']
        )->where(
            'rl.related_id = ?',
            $product->getId()
        );
    }

    /**
     * Retrieve true if Display Related Backgrounds enabled
     * @return boolean
     */
    public function displayBackgrounds()
    {
        return (bool) $this->_scopeConfig->getValue(
            'dnbbackground/product_page/related_backgrounds_enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve backgrounds instance
     *
     * @return \Designnbuy\Background\Model\Category
     */
    public function getProduct()
    {
        if (!$this->hasData('product')) {
            $this->setData('product',
                $this->_coreRegistry->registry('current_product')
            );
        }
        return $this->getData('product');
    }

    /**
     * Get Block Identities
     * @return Array
     */
    public function getIdentities()
    {
        return [\Magento\Catalog\Model\Product::CACHE_TAG . '_relatedbackgrounds_'.$this->getBackground()->getId()  ];
    }
}
