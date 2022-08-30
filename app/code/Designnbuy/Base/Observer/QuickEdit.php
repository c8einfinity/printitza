<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Designnbuy\Base\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;

use Magento\Framework\Registry;
class QuickEdit implements ObserverInterface
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Product
     */
    private $product;

    /**
     * @var design
     */
    private $design;

    /**
     * @var \Designnbuy\Base\Helper\Data
     */
    private $dnbBaseHelper;

    /**
     * @var \Designnbuy\Canvas\Model\Canvas
     */
    private $_canvas;

    /**
     * @var \Designnbuy\Merchandise\Model\Merchandise
     */
    private $_merchandise;

    public function __construct(
        Registry $registry,
        \Designnbuy\Base\Helper\Data $dnbBaseHelper,
        \Designnbuy\Canvas\Model\Canvas $canvas,
        \Designnbuy\Merchandise\Model\Merchandise $merchandise
    ){
        $this->registry = $registry;
        $this->dnbBaseHelper = $dnbBaseHelper;
        $this->_canvas = $canvas;
        $this->_merchandise = $merchandise;
    }


    /**
     *
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        $layout = $observer->getLayout();
        $_product = $this->getProduct();
        $_design = $this->getDesign();
        if(is_null($_design) && $_product && $_product->getId()){
            if($_product->getAttributeSetId() == $this->dnbBaseHelper->getCustomProductAttributeSetId() && $this->_merchandise->isQuickEditEnable($_product)){
                $block = $layout->getBlock('product.info.media.image');
                if ($block) {
                    $block->setTemplate('Designnbuy_Merchandise::catalog/product/view/quickedit/view.phtml');
                }

                $block = $layout->getBlock('pretemplate.quickedit');
                if ($block) {
                    $block->setTemplate('Designnbuy_Merchandise::catalog/product/view/quickedit/property.phtml');
                }
            } elseif ($_product->getAttributeSetId() == $this->dnbBaseHelper->getCustomCanvasAttributeSetId()  && $this->_canvas->isQuickEditEnable($_product)){
                $block = $layout->getBlock('product.info.media.image');
                if ($block) {
                    $block->setTemplate('Designnbuy_Canvas::catalog/product/view/quickedit/view.phtml');
                }

                $block = $layout->getBlock('pretemplate.quickedit');
                if ($block) {
                    $block->setTemplate('Designnbuy_Canvas::catalog/product/view/quickedit/property.phtml');
                }
            }
        }

       
    }

    /**
     * @return Product
     */
    private function getProduct()
    {
        if (is_null($this->product)) {
            $this->product = $this->registry->registry('product');
        }
        return $this->product;
    }

    public function getProductName()
    {
        return $this->getProduct()->getName();
    }

    public function getDesign()
    {
        if (is_null($this->design)) {
            $this->design = $this->registry->registry('current_designer_design_view');
        }
        return $this->design;
    }

}
