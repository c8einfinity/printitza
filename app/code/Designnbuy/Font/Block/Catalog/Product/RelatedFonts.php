<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Font\Block\Catalog\Product;

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
            'dnbfont/product_page/number_of_related_fonts',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if (!$pageSize) {
            $pageSize = 5;
        }
        $this->setPageSize($pageSize);

        parent::_prepareFontCollection();

        $product = $this->getProduct();
        $this->_fontCollection->getSelect()->joinLeft(
            ['rl' => $product->getResource()->getTable('designnbuy_font_font_relatedproduct')],
            'main_table.font_id = rl.font_id',
            ['position']
        )->where(
            'rl.related_id = ?',
            $product->getId()
        );
    }

    /**
     * Retrieve true if Display Related Fonts enabled
     * @return boolean
     */
    public function displayFonts()
    {
        return (bool) $this->_scopeConfig->getValue(
            'dnbfont/product_page/related_fonts_enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve fonts instance
     *
     * @return \Designnbuy\Font\Model\Category
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
        return [\Magento\Catalog\Model\Product::CACHE_TAG . '_relatedfonts_'.$this->getFont()->getId()  ];
    }
}
