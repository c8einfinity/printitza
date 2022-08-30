<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Block\Adminhtml\Role\Tab;

class Products extends \Magento\Backend\Block\Widget\Form\Generic
{
    const MODE_ANY = 0;
    const MODE_SELECTED = 1;
    const MODE_MY = 2;

    protected function _prepareForm()
    {
        /** @var \Amasty\Rolepermissions\Model\Rule $model */
        $model = $this->_coreRegistry->registry('amrolepermissions_current_rule');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('rule_');

        if ($model->getProducts() == \Amasty\Rolepermissions\Model\Rule::PRODUCT_ACCESS_MODE_ANY)
            $modeValue = self::MODE_ANY;
        else if ($model->getProducts() == \Amasty\Rolepermissions\Model\Rule::PRODUCT_ACCESS_MODE_MY)
            $modeValue = self::MODE_MY;
        else
            $modeValue = self::MODE_SELECTED;

        $fieldset = $form->addFieldset('amrolepermissions_products_fieldset', ['legend' => __('Product Access')]);

        $grid = $this->getChildBlock('grid');

        $mode = $fieldset->addField('amrolepermissions[products_access_mode]', 'select', [
            'label' => __('Allow Access To'),
            'id'    => 'amrolepermissions[products_access_mode]',
            'name'  => 'amrolepermissions[products_access_mode]',
            'values'=> [
                self::MODE_ANY => __('All Products'),
                self::MODE_SELECTED => __('Selected Products'),
                self::MODE_MY => __('Own Created Products'),
            ],
            'value' => $modeValue
        ]);

        $fieldset->addField('products_list', 'hidden', [
            'after_element_html' => "<div>{$grid->toHtml()}</div>",
        ]);

        $this->setForm($form);

        $this->setChild('form_after', $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Form\Element\Dependence')
            ->addFieldMap($mode->getHtmlId(), $mode->getName())
            ->addFieldMap('amrolepremissions_allowed_product_grid', 'amrolepremissions_allowed_product_grid')
            ->addFieldDependence(
                'amrolepremissions_allowed_product_grid',
                $mode->getName(),
                self::MODE_SELECTED
            )
        );

        return parent::_prepareForm();
    }
}
