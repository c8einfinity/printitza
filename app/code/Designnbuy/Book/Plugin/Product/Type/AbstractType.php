<?php
namespace Designnbuy\Book\Plugin\Product\Type;
 
class AbstractType
{    
    const OPTION_PREFIX = 'option_';

    public function afterGetOrderOptions(\Magento\Catalog\Model\Product\Type\AbstractType $subject, $result, $product)
    {
        $optionIds = $product->getCustomOption('option_ids');
        if ($optionIds) {
            foreach (explode(',', $optionIds->getValue()) as $key => $optionId) {
                $option = $product->getOptionById($optionId);
                if ($option) {
                    $confItemOption = $product->getCustomOption(self::OPTION_PREFIX . $option->getId());

                    $group = $option->groupFactory($option->getType())
                        ->setOption($option)
                        ->setProduct($product)
                        ->setConfigurationItemOption($confItemOption);

                    $result['options'][$key]['price_data'] = $group->getOptionPrice($confItemOption->getValue(),0);
                }
            }
        }
        
        return $result;
    }
}