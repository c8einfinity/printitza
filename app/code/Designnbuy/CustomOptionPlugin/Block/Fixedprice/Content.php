<?php
namespace Designnbuy\CustomOptionPlugin\Block\Fixedprice;
/**
 * Class Lists
 * @package MAGENTO\Webpos\Block\Customer
 */
class Content extends \Magento\Framework\View\Element\Template
{
    /**
     * Lists constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $layoutProcessors
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $layoutProcessors = [],
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_registry = $registry;
        $this->layoutProcessors = $layoutProcessors;
    }
    /**
     * @return string
     */
    public function getJsLayout()
    {
        foreach ($this->layoutProcessors as $processor) {
            $this->jsLayout = $processor->process($this->jsLayout);
        }
        return parent::getJsLayout();
    }

    public function getCurrentProduct()
    {
        return $this->_registry->registry('current_product');
    }

    public function getFixquantityOption()
    {
        $fixquantity = [];
        $product = $this->_registry->registry('product');
        foreach ($product->getOptions() as $_option) {
            $customoptionsDesigntoolTypeLabel = $_option->getDesigntoolType();
            if($customoptionsDesigntoolTypeLabel == "quantity")	{
                $values = $_option->getValues();
                $fixquantity['id'] = $_option->getId();
                foreach ($values as $_value) {
                    $fixquantityTitle = isset($_value['designtool_title']) && $_value['designtool_title'] != '' ? $_value['designtool_title'] : $_value['title'];

                    $fixquantity['fixquantity'][$_value['option_type_id']] = explode('X',strtoupper($fixquantityTitle));
                }
                return $fixquantity;
            }
        }

        return $fixquantity;
    }

}