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
 * Designidea crosssell designideas block
 */
class CrosssellDesignideas extends \Designnbuy\Designidea\Block\Designidea\DesignideaList\AbstractList
{

     public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Cms\Model\Template\FilterProvider $filterProvider,
        \Designnbuy\Designidea\Model\ResourceModel\Designidea\CollectionFactory $designideaCollectionFactory,
        \Designnbuy\Designidea\Model\Url $url,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Designnbuy\Designidea\Model\DesignideaFactory $designideaFactory,
        array $data = []
    ) {
        parent::__construct($context, $coreRegistry, $filterProvider, $designideaCollectionFactory, $url, $data);
        $this->_checkoutSession = $checkoutSession;
        $this->_designideaFactory = $designideaFactory;
    }


    /**
     * Prepare designidea collection
     *
     * @return void
     */
    protected function _prepareDesignideaCollection()
    {
        parent::_prepareDesignideaCollection();
        $crossSellIds = [];
        $designideaIds = $this->_getCartDesignIds();
        foreach ($designideaIds as $designideaId) {
            $designideaModel = $this->_designideaFactory->create();
            $designidea = $designideaModel->load($designideaId);
            if(count($designidea->getCrosssellDesignideas()->getAllIds()) > 0){
                $crossSellIds[] = $designidea->getCrosssellDesignideas()->getAllIds();
            }
        }

        if(count($crossSellIds) == 0 ){
            $crossSellIds[] = 0;
        }
        $this->_designideaCollection->addAttributeToFilter('entity_id',$crossSellIds);
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
     * Get ids of products that are in cart
     *
     * @return array
     */
    protected function _getCartDesignIds()
    {
        $designideaIds = [];
        $designIdeaIds = [];
        foreach ($this->getQuote()->getAllItems() as $item) {
            $itemOptions = (array) json_decode($item->getOptionByCode('info_buyRequest')->getValue());
            if(isset($itemOptions['designer_design_id']) && !empty($itemOptions['designer_design_id'])) {
                if($itemOptions['toolType'] == 'web2print'){
                    $designideaIds[] = $itemOptions['designer_design_id'];
                }else{
                    $designideaIds[] = $itemOptions['designer_design_id'];
                } 
            }
        }
        return $designideaIds;
    }

    public function displayDesignideas()
    {
        return (bool) $this->_scopeConfig->getValue(
            'dnbdesignidea/designidea_view/crosssell_designideas/enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
