<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Template\Block\Catalog\Product;

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
        /*$pageSize = (int) $this->_scopeConfig->getValue(
            'dnbtemplate/product_page/number_of_related_templates',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if (!$pageSize) {
            $pageSize = 5;
        }
        $this->setPageSize($pageSize);*/
        
        parent::_prepareTemplateCollection();

       /* $product = $this->getProduct();
        $this->_templateCollection->getSelect()->joinLeft(
            ['rl' => $product->getResource()->getTable('designnbuy_template_template_relatedproduct')],
            'main_table.template_id = rl.template_id',
            ['position']
        )->where(
            'rl.related_id = ?',
            $product->getId()
        );*/
    }

    /**
     * Retrieve true if Display Related Templates enabled
     * @return boolean
     */
    public function displayTemplates()
    {
        $_product = $this->getProduct();
        if($_product->getCanvasPersonalizeOption() == 4 && $_product->getEnableCustomHeightWidth() != 1){ // 4 = Multiple Design Templates
            return true;
        }
        return false;
        return (bool) $this->_scopeConfig->getValue(
            'dnbtemplate/product_page/related_templates_enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve templates instance
     *
     * @return \Designnbuy\Template\Model\Category
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
        return [\Magento\Catalog\Model\Product::CACHE_TAG . '_relatedtemplates_'.$this->getTemplate()->getId()  ];
    }


    public function getSizeOption()
    {
        $size = [];
        $product = $this->_coreRegistry->registry('product');
        foreach ($product->getOptions() as $_option) {
            $customoptionsDesigntoolTypeLabel = $_option->getDesigntoolType();
            if($customoptionsDesigntoolTypeLabel == "sizes")	{
                $values = $_option->getValues();
                $size['id'] = $_option->getId();
                foreach ($values as $_value) {
                    $sizeTitle = isset($_value['designtool_title']) && $_value['designtool_title'] != '' ? $_value['designtool_title'] : $_value['title'];
                   
                    $size['size'][$_value['option_type_id']] = explode('X',strtoupper($sizeTitle));
                }
                return $size;
            }
        }

        return $size;
    }
}
