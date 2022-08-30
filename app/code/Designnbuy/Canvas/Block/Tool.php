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
class Tool extends \Magento\Framework\View\Element\Template
{

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Canvas factory
     *getCurrentTemplate
     * @var \Designnbuy\Canvas\Model\Canvas
     */
    protected $_canvas;

    /**
     * Template factory.
     *
     * @var \Designnbuy\Template\Model\TemplateFactory
     */
    protected $_templateFactory;

    /**
     * Customer Design factory.
     *
     * @var \Designnbuy\Customer\Model\DesignFactory
     */
    protected $_designFactory;

    /**
     * @var \Magento\Base\Helper\Output
     */
    protected $_outputHelper;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $_cart;

    /**
     * @var \Designnbuy\Base\Service\MobileDetect
     */
    protected $_mobileDetect;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Designnbuy\Canvas\Model\Canvas $canvas,
        \Designnbuy\Template\Model\TemplateFactory $templateFactory,
        \Designnbuy\Customer\Model\DesignFactory $designFactory,
        \Magento\Checkout\Model\Cart $cart,
        \Designnbuy\Base\Helper\Output $outputHelper,
        \Designnbuy\Base\Service\MobileDetect $mobileDetect
    ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->_canvas = $canvas;
        $this->_templateFactory = $templateFactory;
        $this->_designFactory = $designFactory;
        $this->_cart = $cart;
        $this->_outputHelper = $outputHelper;
        $this->_mobileDetect = $mobileDetect;
        $this->setTemplatePHTML();
    }
    /**
     * Preparing global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    public function setTemplatePHTML()
    {
        $template = 'Designnbuy_Canvas::tool.phtml';
        if ($this->_mobileDetect->isMobile() || $this->_mobileDetect->isTablet()) {
            $template = 'Designnbuy_Canvas::tool_mobile.phtml';
        }
        $this->setTemplate($template);
    }

    public function getProductId()
    {
        $productId = $this->getRequest()->getParam('id');
        return $productId;
    }

    public function getProductData()
    {
        $productId = $this->getProductId();
        $result = $this->_canvas->getProductData($productId, $area = 'front');
        return $result;
    }

    /**
     * Get templates instance
     *
     * @return \Designnbuy\Template\Model\Template
     */
    public function getCurrentTemplate()
    {
        return $this->getData('current_template');
    }


    /**
     * Set templates instance
     *
     * @return \Designnbuy\Template\Model\Template
     */
    public function setCurrentTemplate($template)
    {
        if (!$this->hasData('current_template')) {
            $this->setData('current_template',
                $template
            );
        }
        return $this->getData('current_template');
    }

    public function getCurrentTemplateSVG()
    {
        $designDir = $this->_outputHelper->getTemplateDesignsDir();
        $templateId = $this->getRequest()->getParam('template_id');

        $svgs = [];

        $fromQuickedit = $this->getRequest()->getParam('from_quickedit');
        $quickEditDesigns = $this->getRequest()->getParam('quick_edit_designs');


        if(isset($templateId) && $templateId != ''){
            $templateModel = $this->_templateFactory->create();
            $templateModel->load($templateId);
            $templateModel->setStoreId($this->_storeManager->getStore()->getId());
            $this->setCurrentTemplate($templateModel);
            if($fromQuickedit && !empty($quickEditDesigns)){
                $designDir = $this->_outputHelper->getCartDesignsDir();
                foreach ($quickEditDesigns as $quickEditDesign) {
                    if($quickEditDesign != '' && file_exists($designDir.$quickEditDesign)){
                        $formatedSVG = $this->_outputHelper->getFormatedSVG($designDir.$quickEditDesign);
                        $svgs[] = $formatedSVG;
                        unlink($designDir.$quickEditDesign);
                    }
                }
            } else {
                $templateSVGs = explode(",", $templateModel->getSvg());
                if(isset($templateSVGs) && !empty($templateSVGs)){
                    foreach ($templateSVGs as $templateSVG) {
                        if($templateSVG != '' && file_exists($designDir.$templateSVG)){
                            $formatedSVG = $this->_outputHelper->getFormatedSVG($designDir.$templateSVG);
                            $svgs[] = $formatedSVG;
                        }
                    }
                }
            }
        }
        return $svgs;
    }

    public function getCurrentCustomerDesignSVG()
    {
        $designDir = $this->_outputHelper->getCustomerDesignsDir();
        $designId = $this->getRequest()->getParam('design_id');

        $design = [];
        if(isset($designId) && $designId != ''){
            $designModel = $this->_designFactory->create();
            $designModel->load($designId);
            $options = $designModel->getOptions();

            $design['design_id'] = $designModel->getDesignId();
            $design['design_name'] = $designModel->getDesignName();
            $design['options'] = json_decode($options, true);
            $designSVGs = explode(",", $designModel->getSvg());

            if(isset($designSVGs) && !empty($designSVGs)){
                foreach ($designSVGs as $designSVG) {
                    if(file_exists($designDir.$designSVG)){
                        $formatedSVG = $this->_outputHelper->getFormatedSVG($designDir.$designSVG);
                        $design['svg'][] = $formatedSVG;
                    }
                }

            }
        }
        return $design;
    }

    public function getQuoteItemDesign()
    {
        /*$item = $this->quoteItemFactory->create();
        $item->load($itemId);*/
        $designDir = $this->_outputHelper->getCartDesignsDir();
        $itemId = $this->getRequest()->getParam('item_id');
        $quoteOptions = [];
        $quoteItem = $this->_cart->getQuote()->getItemById($itemId);
        if($quoteItem){
            $quoteOptions['qty'] = $quoteItem->getQty();
            $infoBuyRequest = $quoteItem->getOptionByCode('info_buyRequest');
            $itemOptions = (array) json_decode($quoteItem->getOptionByCode('info_buyRequest')->getValue(), true);

            if(isset($itemOptions['svg_file']) && !empty($itemOptions['svg_file'])){
                $quoteOptions['svg_file'] = $itemOptions['svg_file'];
            }
            if(isset($itemOptions['vdp_file']) && !empty($itemOptions['vdp_file'])){
                $quoteOptions['vdp_file'] = $itemOptions['vdp_file'];
            }
            if(isset($itemOptions['options']) && !empty($itemOptions['options'])){
                $quoteOptions['options'] = $itemOptions['options'];
            }
            if(isset($itemOptions['svg']) && !empty($itemOptions['svg'])){
                $designSVGs = [];
                $designSVGs = explode(',', $itemOptions['svg']);
                foreach ($designSVGs as $designSVG) {
                    if(file_exists($designDir.$designSVG)){
                        $formatedSVG = $this->_outputHelper->getFormatedSVG($designDir.$designSVG);
                        $quoteOptions['svg'][] = $formatedSVG;
                    }
                }

            }
        }
        return $quoteOptions;
    }

}
