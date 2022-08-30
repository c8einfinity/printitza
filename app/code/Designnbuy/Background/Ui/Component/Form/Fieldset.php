<?php
namespace Designnbuy\Background\Ui\Component\Form;


use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Ui\Component\Form\FieldFactory;
use Magento\Ui\Component\Form\Fieldset as BaseFieldset;

class Fieldset extends BaseFieldset
{
    /**
     * @var FieldFactory
     */
    private $fieldFactory;

    public function __construct(
        ContextInterface $context,
        array $components = [],
        array $data = [],
        FieldFactory $fieldFactory)
    {
        parent::__construct($context, $components, $data);
        $this->fieldFactory = $fieldFactory;
    }

    /**
     * Get components
     *
     * @return UiComponentInterface[]
     */
    public function getChildComponents()
    {
        $fields = [
            [
                'label' => __('Field Label From Code'),
                'value' => __('Field Value From Code'),
                'formElement' => 'input',
            ],
            [
                'label' => __('Another Field Label From Code'),
                'value' => __('Another Field Value From Code'),
                'formElement' => 'input',
            ],
            [
                'label' => __('Yet Another Field Label From Code'),
                'value' => __('Yet Another Field Value From Code'),
                'formElement' => 'input',
            ]
        ];

        foreach ($fields as $k => $fieldConfig) {
            $fieldInstance = $this->fieldFactory->create();
            $name = 'my_dynamic_field_' . $k;

            $fieldInstance->setData(
                [
                    'config' => $fieldConfig,
                    'name' => $name
                ]
            );

            $fieldInstance->prepare();
            $this->addComponent($name, $fieldInstance);
        }

        return parent::getChildComponents();
    }
}
