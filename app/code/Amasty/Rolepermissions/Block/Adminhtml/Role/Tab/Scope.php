<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Block\Adminhtml\Role\Tab;

class Scope extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    const MODE_NONE = 0;
    const MODE_SITE = 1;
    const MODE_VIEW = 2;

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Get tab label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Advanced: Scope');
    }

    /**
     * Get tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Whether tab is available
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Whether tab is visible
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    protected function _prepareForm()
    {
        /** @var \Amasty\Rolepermissions\Model\Rule $model */
        $model = $this->_coreRegistry->registry('amrolepermissions_current_rule');

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset('amrolepermissions_scope_fieldset', ['legend' => __('Choose Access Scope')]);

        if ($model->getScopeWebsites())
            $model->setScopeMode(self::MODE_SITE);
        else if ($model->getScopeStoreviews())
            $model->setScopeMode(self::MODE_VIEW);
        else
            $model->setScopeMode(self::MODE_NONE);

        $mode = $fieldset->addField('scope_mode', 'select',
            [
                'label' => __('Limit Access To'),
                'id'    => 'scope_mode',
                'values'=> [
                    self::MODE_NONE => __('Allow All Stores'),
                    self::MODE_SITE => __('Specified Websites'),
                    self::MODE_VIEW => __('Specified Store Views'),
                ],
            ]
        );

        $websites = $fieldset->addField(
            'scope_websites',
            'multiselect',
            [
                'name' => 'amrolepermissions[scope_websites]',
                'label' => __('Websites'),
                'title' => __('Websites'),
                'values' => $this->_systemStore->getWebsiteValuesForForm()
            ]
        );
        $renderer = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element'
        );
        $websites->setRenderer($renderer);


        $stores = $fieldset->addField(
            'scope_storeviews',
            'multiselect',
            [
                'name' => 'amrolepermissions[scope_storeviews]',
                'label' => __('Store Views'),
                'title' => __('Store Views'),
                'values' => $this->_systemStore->getStoreValuesForForm(false, false),
            ]
        );
        $stores->setRenderer($renderer);

        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock(
                'Magento\Backend\Block\Widget\Form\Element\Dependence'
            )

            ->addFieldMap($mode->getHtmlId(), $mode->getName())
            ->addFieldMap($websites->getHtmlId(), $websites->getName())
            ->addFieldMap($stores->getHtmlId(), $stores->getName())
            ->addFieldDependence(
                $websites->getName(),
                $mode->getName(),
                self::MODE_SITE
            )
            ->addFieldDependence(
                $stores->getName(),
                $mode->getName(),
                self::MODE_VIEW
            )
        );

        $form->setValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
