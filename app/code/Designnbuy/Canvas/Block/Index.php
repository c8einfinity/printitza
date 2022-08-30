<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Canvas\Block;

use Magento\Store\Model\ScopeInterface;

/**
 * Template index block
 */
class Index extends \Magento\Framework\View\Element\Template
{
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Canvas factory
     *
     * @var \Designnbuy\Canvas\Model\Canvas
     */
    protected $_canvas;

    /**
     * @var \Designnbuy\Base\Service\MobileDetect
     */
    protected $_mobileDetect;

    /**
     * Output Helper Class
     *
     * @var \Designnbuy\Base\Helper\Output
     */
    protected $_outputHelper;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Designnbuy\Canvas\Model\Canvas $canvas,
        \Designnbuy\Base\Service\MobileDetect $mobileDetect,
        \Designnbuy\Base\Helper\Output $outputHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_storeManager = $storeManager;
        $this->_canvas = $canvas;
        $this->_mobileDetect = $mobileDetect;
        $this->_outputHelper = $outputHelper;
    }


    /**
     * Preparing global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        //$this->_addBreadcrumbs();
        $this->pageConfig->getTitle()->set($this->_getConfigValue('title'));
        $this->pageConfig->setKeywords($this->_getConfigValue('meta_keywords'));
        $this->pageConfig->setDescription($this->_getConfigValue('meta_description'));

		//$this->pageConfig->setMetadata('viewport','');
        //if ($this->_mobileDetect->isMobile() && !$this->_mobileDetect->isTablet()) {
            $this->pageConfig->setMetadata('viewport','user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=device-width, height=device-height, target-densitydpi=device-dpi');
        //}
        $this->pageConfig->addRemotePageAsset(
            $this->_storeManager->getStore()->getBaseUrl(),
            'canonical',
            ['attributes' => ['rel' => 'canonical']]
        );
        return parent::_prepareLayout();
    }

    /**
     * Retrieve template title
     * @return string
     */
    protected function _getConfigValue($param)
    {
        return $this->_scopeConfig->getValue(
            'canvas/index_page/'.$param,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getStoreConfigValue($param)
    {
        return $this->_scopeConfig->getValue(
            'canvas/index_page/'.$param,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Prepare breadcrumbs
     *
     * @param  string $title
     * @param  string $key
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    protected function _addBreadcrumbs($title = null, $key = null)
    {
        if ($breadcrumbsBlock = $this->getBreadcrumbsBlock()) {
            $breadcrumbsBlock->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link' => $this->_storeManager->getStore()->getBaseUrl()
                ]
            );

            $merchandiseTitle = $this->_scopeConfig->getValue(
                'merchandise/index_page/title',
                ScopeInterface::SCOPE_STORE
            );
            $breadcrumbsBlock->addCrumb(
                'merchandise',
                [
                    'label' => __($merchandiseTitle),
                    'title' => __($merchandiseTitle),
                    'link' => $this->_storeManager->getStore()->getBaseUrl()
                ]
            );

            if ($title) {
                $breadcrumbsBlock->addCrumb($key ?: 'merchandise_item', ['label' => $title, 'title' => $title]);
            }
        }
    }

    /**
     * Retrieve breadcrumbs block
     *
     * @return mixed
     */
    protected function getBreadcrumbsBlock()
    {
        if ($this->_scopeConfig->getValue('web/default/show_cms_breadcrumbs', ScopeInterface::SCOPE_STORE)) {
            return $this->getLayout()->getBlock('breadcrumbs');
        }

        return false;
    }



    public function getIframeUrl() {
        /*$qty = 1;
        $params = [];

        if ($this->getRequest()->getParam('id')) {
            $productId = $this->getRequest()->getParam('id');
            $params['product'] = $productId;
        }
        $super_attribute = $this->getRequest()->getParam('super_attribute');
        if(isset($super_attribute)){
            $params['super_attribute'] = $super_attribute;
        }

        if ($this->getRequest()->getParam('qty')) {
            $qty = $this->getRequest()->getParam('qty');
            $params['qty'] = $qty;
        }

        if ($this->getRequest()->getParam('design_id')) {
            $design_id = $this->getRequest()->getParam('design_id');
            $params['design_id'] = $design_id;
        }

        if ($this->getRequest()->getParam('cart_id')) {
            $quoteItemId = $this->getRequest()->getParam('cart_id');
            $params['item_id'] = $quoteItemId;
        }*/
        $params = $this->getRequest()->getParams();

        $cnt = 0;
        $designSvgNames = array();
        $timeStamp = time();
        if(array_key_exists('svgTextArea', $params) && isset($params['svgTextArea']) && !empty($params['svgTextArea'])){
            $designDir = $this->_outputHelper->getCartDesignsDir();
            $svgStr = $params['svgTextArea'];
            foreach($svgStr as $svg){
                $svgImageName = "design_".$timeStamp.'_'.$cnt.'.svg';
                $designSvgNames[$cnt] = $svgImageName;
                $svgImagePath = $designDir . $svgImageName;
                $this->_outputHelper->saveSvg($svgImagePath, $svg);
                $result['quick_edit_designs'][] = $svgImageName;
                $cnt++;
            }
            $params['quick_edit_designs'] = $designSvgNames;
            $params['from_quickedit'] = 1;
        }

        $params['svgTextArea'] = '';
        $params['previewSvg'] = '';

        return $this->getUrl('canvas/tool/index').'?'.http_build_query($params);
        //return $this->getUrl('merchandise/tool/index', $params);

        /*if(isset($quoteItemId) && isset($cart_design_id) && $quoteItemId != '' && $cart_design_id != '') {
            $quoteItem = null;
            if ($quoteItemId) {
                $quoteItem = $this->cart->getQuote()->getItemById($quoteItemId);
            }

            // $quoteItem = $this->cart->getQuote()->getItemById($quoteItemId);
            $productType = $quoteItem->getProductType();
            $extra = array();
            $color_id = 0;
            $size_id = 0;
            if($productType == 'configurable') {
                $option = $quoteItem->getOptionByCode('simple_product');
                $_product = $this->_modelProductFactory->create()->load($option->getProductId());
                $colorOptionId = $_product->getColor();
                $sizeOptionId = $_product->getSize();
                $buyRequest = $quoteItem->getBuyRequest();
                $superAttribute = $buyRequest->getSuperAttribute();

                foreach($superAttribute as $option_key => $option_value){
                    if ($option_value == $colorOptionId) {
                        $color_id = $colorOptionId;
                    } else if ($option_value == $sizeOptionId) {
                        $size_id = $sizeOptionId;
                    } else {
                        $extra = $extra + array($option_key => $option_value);
                    }
                }
            }
            $qty = (int)$quoteItem->getQty();
            $product_id = $quoteItem->getProduct()->getId();
            $extraoptions = base64_encode(serialize($extra));
            $plateform = 'magento';
            $siteBaseUrl = base64_encode(serialize($this->_modelStoreManagerInterface->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB)));
            $language_id = $this->_scopeConfig->getValue(
                \Magento\Directory\Helper\Data::XML_PATH_DEFAULT_LOCALE,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $this->_modelStoreManagerInterface->getStore()->getId()
            );
            $url = $this->_modelStoreManagerInterface->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB). 'designnbuy/pcstudio/editCart?product_id=' . $product_id . '&qty=' . $qty . '&color_id=' . $color_id . '&size_id=' . $size_id . '&extraoptions=' . $extraoptions . '&cart_id=' . $quoteItemId . '&cart_design_id=' . $cart_design_id . '&plateform=' . $plateform . '&siteBaseUrl=' . $siteBaseUrl . '&language_id=' . $language_id;

        }else if($design_id != '') {
            $plateform = 'magento';
            $siteBaseUrl = base64_encode(serialize($this->_modelStoreManagerInterface->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB)));
            $language_id = $this->_scopeConfig->getValue(
                \Magento\Directory\Helper\Data::XML_PATH_DEFAULT_LOCALE,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $this->_modelStoreManagerInterface->getStore()->getId()
            );
            $url = $this->_modelStoreManagerInterface->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB) . 'designnbuy/pcstudio/editMyDesign?design_id=' . $design_id . '&plateform=' . $plateform . '&siteBaseUrl=' . $siteBaseUrl . '&language_id=' . $language_id;
        } else {
            $configProduct = $this->_modelProductFactory->create()->load($product_id);
            $extra = [];
            $color_id = 0;
            $size_id = 0;
            if ($configProduct->getTypeId() == 'configurable') {

                $productAttributeOptions = $this->_typeConfigurableFactory->getConfigurableAttributesAsArray($configProduct);

                $option_attr = $this->_modelDesignFactory->getOptionAttr('option_abbreviation');
                $colorkey = $this->getArray($productAttributeOptions, 'label', $option_attr['color_option']);
                $sizekey = $this->getArray($productAttributeOptions, 'label', $option_attr['size_option']);

                $productModel = $this->_modelProductFactory->create();
                $colorAttributeId = null;
                $sizeAttributeId = null;
                if($colorkey)
                    $colorAttributeId = $productModel->getResource()->getAttribute($productAttributeOptions[$colorkey]['attribute_code'])->getAttributeId();
                if($sizekey)
                    $sizeAttributeId = $productModel->getResource()->getAttribute($productAttributeOptions[$sizekey]['attribute_code'])->getAttributeId();


                if ($option) {
                    foreach ($option as $option_id => $value) {
                        if ($option_id == $colorAttributeId) {
                            $color_id = $value;
                        } else if ($option_id == $sizeAttributeId) {
                            $size_id = $value;
                        } else {
                            $extra = $extra + [$option_id => $value];
                        }
                    }
                }
            }

            $extraoptions = base64_encode(serialize($extra));
            $plateform = 'magento';
            $siteBaseUrl = base64_encode(serialize($this->_modelStoreManagerInterface->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB)));

            $language_id = $this->_scopeConfig->getValue(
                \Magento\Directory\Helper\Data::XML_PATH_DEFAULT_LOCALE,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $this->_modelStoreManagerInterface->getStore()->getId()
            );

            $url = $this->_modelStoreManagerInterface->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB) . 'designnbuy/pcstudio?
            product_id=' . $product_id .
                '&qty=' . $qty .
                '&color_id=' . $color_id .
                '&size_id=' . $size_id .
                '&extraoptions=' . $extraoptions .
                '&plateform=' . $plateform .
                '&siteBaseUrl=' . $siteBaseUrl .
                '&language_id=' . $language_id;
        }
        return $url;*/
    }

}
