<?php

namespace Designnbuy\Template\Block\Adminhtml;

/**
 * Template grid container
 * @category Designnbuy
 * @package  Designnbuy_Template
 * @module   Template
 * @author   Designnbuy Developer
 */
class Template extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Internal constructor, that is called from real constructor
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_template';
        $this->_blockGroup = 'Designnbuy_Template';
        $this->_headerText = __('Templates');
        $this->_addButtonLabel = __('Add New Design');
        parent::_construct();
        //$this->removeButton('add');
    }


    /**
     * Prepare button and grid
     *
     * @return \Magento\Catalog\Block\Adminhtml\Product
     */
    protected function _prepareLayout()
    {
        /*$addButtonProps = [
            'id' => 'add_new_design',
            'label' => __('Add Design'),
            'class' => 'add',
            'button_class' => '',
            'class_name' => 'Magento\Backend\Block\Widget\Button\SplitButton',
            'options' => $this->_getAddDesignButtonOptions(),
        ];
        $this->buttonList->add('add_new', $addButtonProps);*/

        return parent::_prepareLayout();
    }

    /**
     * Retrieve options for 'Add Product' split button
     *
     * @return array
     */
    protected function _getAddDesignButtonOptions()
    {
        $splitButtonOptions = [];
        $types = [
            'template' =>  [
                'label' => 'Template',
                'name' => 'template',
            ],
            'template' =>  [
                'label' => 'Template',
                'name' => 'template',
            ]
        ];

        foreach ($types as $typeId => $type) {
            $splitButtonOptions[$typeId] = [
                'label' => __($type['label']),
                'onclick' => "setLocation('" . $this->_getProductCreateUrl($typeId) . "')",
                'default' => \Magento\Catalog\Model\Product\Type::DEFAULT_TYPE == $typeId,
            ];
        }

        return $splitButtonOptions;
    }

    /**
     * Retrieve product create url by specified product type
     *
     * @param string $type
     * @return string
     */
    protected function _getProductCreateUrl($type)
    {
        return $this->getUrl(
            'template/*/new',
            ['type' => $type]
        );
    }
}
