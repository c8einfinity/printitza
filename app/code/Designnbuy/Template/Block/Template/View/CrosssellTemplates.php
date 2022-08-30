<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Template\Block\Template\View;

use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\View\Element\AbstractBlock;

/**
 * Template template related templates block
 */
class CrosssellTemplates extends \Designnbuy\Template\Block\Template\TemplateList\AbstractList
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Designnbuy\Template\Model\ResourceModel\Template\CollectionFactory $templateCollectionFactory
     * @param \Designnbuy\Template\Model\Url $url
     * @param \Designnbuy\Template\Model\CategoryFactory $categoryFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Designnbuy\Template\Model\ResourceModel\Template\CollectionFactory $templateCollectionFactory,
        \Designnbuy\Template\Model\Url $url,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Designnbuy\Template\Model\TemplateFactory $templateFactory,
        array $data = []
    ) {
        parent::__construct($context, $coreRegistry, $filterProvider, $templateCollectionFactory, $url, $data);
        $this->_checkoutSession = $checkoutSession;
        $this->_templateFactory = $templateFactory;
    }


    /**
     * Prepare templates collection
     *
     * @return void
     */
    protected function _prepareTemplateCollection()
    {
        /*$pageSize = (int) $this->_scopeConfig->getValue(
            'dnbtemplate/template_view/related_templates/number_of_templates',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $this->_templateCollection = $this->getCurrentTemplate()->getUpsellTemplates()->addAttributeToSelect('title')->addAttributeToSelect('identifier')
            ->addActiveFilter()
            ->setPageSize($pageSize ?: 5);*/

        parent::_prepareTemplateCollection();
        $crossSellIds = [];
        $templateIds = $this->_getCartDesignIds();
        foreach ($templateIds as $templateId) {
            $templateModel = $this->_templateFactory->create();
            $template = $templateModel->load($templateId);
            if(count($template->getCrosssellTemplates()->getAllIds()) > 0){
                $crossSellIds[] = $template->getCrosssellTemplates()->getAllIds();
            }
        }
        
        if(count($crossSellIds) == 0 ){
            $crossSellIds[] = 0;
        }
        $this->_templateCollection->addAttributeToFilter('entity_id',$crossSellIds);
    }

    /**
     * Get ids of products that are in cart
     *
     * @return array
     */
    protected function _getCartDesignIds()
    {
        $templateIds = [];
        $designIdeaIds = [];

        //$ids = $this->getData('_cart_design_ids');
        //if ($ids === null) {
            //$ids = [];
            foreach ($this->getQuote()->getAllItems() as $item) {
                $itemOptions = (array) json_decode($item->getOptionByCode('info_buyRequest')->getValue());
                
                if(isset($itemOptions['designer_design_id']) && !empty($itemOptions['designer_design_id'])) {
                    if($itemOptions['toolType'] == 'web2print'){
                        $templateIds[] = $itemOptions['designer_design_id'];
                    } else {
                        //$designIdeaIds[] = $itemOptions['designer_design_id'];
                    }
                }
            }
            
            //$this->setData('_cart_design_ids', $ids);
        //}
        //return $ids;
        return $templateIds;
    }

    /**
     * Get quote instance
     *
     * @return \Magento\Quote\Model\Quote
     * @codeCoverageIgnore
     */
    public function getQuote()
    {
        return $this->_checkoutSession->getQuote();
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

}
