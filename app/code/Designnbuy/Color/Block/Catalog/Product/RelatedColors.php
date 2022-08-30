<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Color\Block\Catalog\Product;

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
            'dnbcolor/product_page/number_of_related_colors',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if (!$pageSize) {
            $pageSize = 5;
        }
        $this->setPageSize($pageSize);

        parent::_prepareColorCollection();

        $product = $this->getProduct();
        $this->_colorCollection->getSelect()->joinLeft(
            ['rl' => $product->getResource()->getTable('designnbuy_color_color_relatedproduct')],
            'main_table.color_id = rl.color_id',
            ['position']
        )->where(
            'rl.related_id = ?',
            $product->getId()
        );
    }

    /**
     * Retrieve true if Display Related Colors enabled
     * @return boolean
     */
    public function displayColors()
    {
        return (bool) $this->_scopeConfig->getValue(
            'dnbcolor/product_page/related_colors_enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve colors instance
     *
     * @return \Designnbuy\Color\Model\Category
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
        return [\Magento\Catalog\Model\Product::CACHE_TAG . '_relatedcolors_'.$this->getColor()->getId()  ];
    }
}
