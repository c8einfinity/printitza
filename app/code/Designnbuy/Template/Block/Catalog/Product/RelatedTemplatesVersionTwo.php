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
class RelatedTemplatesVersionTwo extends \Designnbuy\Template\Block\Catalog\Product\RelatedTemplates
{

    /**
     * Prepare templates collection
     *
     * @return void
     */
    protected function _prepareTemplateCollection()
    {
        $page = ($this->getRequest()->getParam('p'))? $this->getRequest()->getParam('p') : 1;

        $pageSize=($this->getRequest()->getParam('limit'))? $this->getRequest
        ()->getParam('limit') : $this->fixPageSize();

        $this->_templateCollection = $this->_templateCollectionFactory->create()
            ->addAttributeToSelect('*')
            ->addActiveFilter()
            //->addProductFilter($product)
            ->addTemplateFilter()
            ->addWebSiteFilter($this->_storeManager->getWebsite()->getId(), false)
            ->setStoreId($this->_storeManager->getStore()->getId());
            
        $product = $this->_coreRegistry->registry('product');
        if($product != null){
            $this->_templateCollection->addProductFilter($product)->setOrder('related_product.position', 'ASC');
        }

        ##@a13 Template wise filter on browse template 
        $defaultSizeWidth = $product->getWidth();
        $defaultSizeHeight = $product->getHeight();
        if($defaultSizeWidth != "" && $defaultSizeHeight != ""){
            $this->_templateCollection->addAttributeToFilter('width', $defaultSizeWidth);
            $this->_templateCollection->addAttributeToFilter('height', $defaultSizeHeight);
        }else {
            $productOptions = $this->getRequest()->getParam('options');
            if(isset($productOptions) && $productOptions != "") {            
                $productOptions = array_flip($productOptions);
                foreach ($productOptions as $optionKey => $value) {                
                    foreach ($product->getOptions() as $key => $value) {
                        $designtoolType = $value->getDesigntoolType();
                        if($designtoolType == 'sizes'){
                            $optionValues = $value->getValues();
                            foreach ($optionValues as $key => $optionVal) {
                                if($optionKey == $optionVal->getId()){
                                    $optionSize = $optionVal->getDesigntoolTitle();
                                }
                            }
                        }    
                    }
                        
                }
            }
            $delimiter = (strpos($optionSize, 'x') !== false) ? 'x' : 'X'; 
            $widthHeight = explode($delimiter, $optionSize);
            if($widthHeight){
                $this->_templateCollection->addAttributeToFilter('width', $widthHeight[0]);
                $this->_templateCollection->addAttributeToFilter('height', $widthHeight[1]);
            }
        }
        ##@a13 end

        if($this->getRequest()->getParam('category_id') && $this->getRequest()->getParam('category_id') != "" && $this->getRequest()->getParam('category_id') != "all"){
            $this->_templateCollection->addAttributeToFilter('category_id',  ['finset' => $this->getRequest()->getParam('category_id')]);
        }
        if($this->getRequest()->getParam('width') && $this->getRequest()->getParam('width') != "" && $this->getRequest()->getParam('height') && $this->getRequest()->getParam('height') != ""){
            //echo 'here'; exit;
            $this->_templateCollection->addAttributeToFilter('width', $this->getRequest()->getParam('width'));
            $this->_templateCollection->addAttributeToFilter('height', $this->getRequest()->getParam('height'));
        }

        if($this->getRequest()->getParam('search') && $this->getRequest()->getParam('search') != ""){
            $this->_templateCollection->addAttributeToFilter('title', array('like' => '%'.$this->getRequest()->getParam('search').'%'));
        }

        //$this->_templateCollection->setPageSize($pageSize);
        //$this->_templateCollection->setCurPage($page);
        
        if($product != null){
            $this->_templateCollection->getSelect()->order('related_product.position', 'ASC');
        }

        //parent::_prepareTemplateCollection();

    }

    public function fixPageSize(){
        return 8;
    }
    
}
