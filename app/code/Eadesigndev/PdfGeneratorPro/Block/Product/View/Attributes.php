<?php

namespace Eadesigndev\PdfGeneratorPro\Block\Product\View;

use Magento\Catalog\Block\Product\View\Attributes as AttributesView;
use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Class Attributes
 * @package Eadesigndev\PdfGeneratorPro\Block\Product\View
 */
class Attributes extends AttributesView
{

        public function __construct(
            \Magento\Framework\View\Element\Template\Context $context,
            \Magento\Framework\Registry $registry,
            PriceCurrencyInterface $priceCurrency,
            array $data = []
        )
        {
            $this->setTemplate('Eadesigndev_PdfGeneratorPro::Product/View/attributes.phtml');
            parent::__construct($context, $registry, $priceCurrency, $data);
        }

    public function setProduct($product)
    {

        $this->_product = $product;
        return $this;
    }

    public function attributeTableSource($product)
    {
        $data = [];
        $attributes = $product->getAttributes();
        foreach ($attributes as $attribute) {
            if ($attribute->getIsVisibleOnFront()) {
                $value = $attribute->getFrontend()->getValue($product);

                if (!$product->hasData($attribute->getAttributeCode())) {
                    continue;
                } elseif ((string)$value == '') {
                    continue;
                } elseif ($attribute->getFrontendInput() == 'price' && is_string($value)) {
                    $value = $this->priceCurrency->convertAndFormat($value);
                }

                if ($value instanceof Phrase || $value) {
                    $data[$attribute->getAttributeCode()] = [
                        'label' => __($attribute->getStoreLabel()),
                        'value' => $value,
                        'code' => $attribute->getAttributeCode(),
                    ];
                }
            }
        }

        return $data;
    }
}
