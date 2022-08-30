<?php
namespace Designnbuy\Canvas\Pricing\Render;

use Magento\Msrp\Pricing\Price\MsrpPrice;
use Magento\Framework\Pricing\Render\PriceBox as BasePriceBox;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Pricing\SaleableInterface;
use Magento\Framework\Pricing\Price\PriceInterface;
use Magento\Framework\Pricing\Render\RendererPool;
use Magento\Catalog\Model\Product\Pricing\Renderer\SalableResolverInterface;
use Magento\Catalog\Pricing\Price\MinimalPriceCalculatorInterface;

class FinalPriceBox extends \Magento\Catalog\Pricing\Render\FinalPriceBox
{
    protected $_registry;

    protected $_helper;

    public function __construct(
        Context $context,
        SaleableInterface $saleableItem,
        PriceInterface $price,
        RendererPool $rendererPool,
        array $data = [],
        SalableResolverInterface $salableResolver = null,
        MinimalPriceCalculatorInterface $minimalPriceCalculator = null,
        \Magento\Framework\Registry $registry,
        \Designnbuy\Base\Helper\Data $helper
    )
    {
        parent::__construct($context, $saleableItem, $price, $rendererPool, $data);
        $this->_registry = $registry;
        $this->_helper = $helper;
    }

    protected function wrapResult($html)
    {
        $result = '';
        $_product = $this->_registry->registry('current_product');
        if ($_product && $_product->hasOptions() && count($_product->getOptions()) > 0 && $this->_helper->isCanvasProduct($_product) || $_product && $_product->hasOptions() && count($_product->getOptions()) > 0 && $_product->getAttributeSetId() == '4') {
            $result = '<label class="label"><span>Price</span></label>';
            $result .= '<div class="price-box ' . $this->getData('css_classes') . '" ' .
                'data-role="priceBox" ' .
                'data-product-id="' . $this->getSaleableItem()->getId() . '"' .
                '>' . $html . '</div>';
        } else {
            $result = '<div class="price-box ' . $this->getData('css_classes') . '" ' .
            'data-role="priceBox" ' .
            'data-product-id="' . $this->getSaleableItem()->getId() . '" ' .
            'data-price-box="product-id-' . $this->getSaleableItem()->getId() . '"' .
            '>' . $html . '</div>';
        }
        return $result;
    }
}