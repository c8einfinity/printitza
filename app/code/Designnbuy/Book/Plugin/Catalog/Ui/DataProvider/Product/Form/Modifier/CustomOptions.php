<?php
namespace Designnbuy\Book\Plugin\Catalog\Ui\DataProvider\Product\Form\Modifier;


class CustomOptions
{

    public function afterModifyMeta(
        \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions $subject,
        $meta
    ) {
        $meta['custom_options']['children']['options']['children']['record']['children']['container_option']['children']['container_common']['children']['custom_text'] =
        $this->getTitleFieldConfig(
            200,
            [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('Unique field sku'),
                            'component' => 'Magento_Catalog/component/static-type-input',
                            'valueUpdate' => 'input',
                            'imports' => [
                                'optionId' => '${ $.provider }:${ $.parentScope }.option_id'
                            ]
                        ],
                    ],
                ],
            ]
        );

        return $meta;
    }

    /**
     * Get config for "Title" fields
     *
     * @param int $sortOrder
     * @param array $options
     * @return array
     */
    protected function getTitleFieldConfig($sortOrder, array $options = [])
    {
        return array_replace_recursive(
            [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('Step'),
                            'componentType' => \Magento\Ui\Component\Form\Field::NAME,
                            'formElement' => \Magento\Ui\Component\Form\Element\Input::NAME,
                            'dataScope' => 'unique_field_sku',
                            'dataType' => \Magento\Ui\Component\Form\Element\DataType\Text::NAME,
                            'sortOrder' => $sortOrder,
                            'validation' => [
                                'required-entry' => false
                            ],
                        ],
                    ],
                ],
            ],
            $options
        );
    }
}
?>