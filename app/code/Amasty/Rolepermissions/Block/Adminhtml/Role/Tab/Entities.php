<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Block\Adminhtml\Role\Tab;

use Magento\Config\Model\Config\Source;

class Entities extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
    protected $optionList;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        Source\Yesno $optionList,
        array $data = []
    ) {
        $this->optionList = $optionList;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Get tab label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Advanced: Affected Entities');
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

        if (!$model->getId()) {
            $model
                ->setLimitOrders(true)
                ->setLimitInvoices(true)
                ->setLimitShipments(true)
                ->setLimitMemos(true)
            ;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset('amrolepermissions_scope_fieldset', ['legend' => __('Affected Entities')]);

        $fieldset->addField(
            'limit_orders', 'select',
            [
                'label'  => __('Limit Access To Orders'),
                'name'   => 'amrolepermissions[limit_orders]',
                'values' => $this->optionList->toOptionArray(),
            ]
        );

        $fieldset->addField(
            'limit_invoices', 'select',
            [
                'label'  => __('Limit Access To Invoices And Transactions'),
                'name'   => 'amrolepermissions[limit_invoices]',
                'values' => $this->optionList->toOptionArray(),
            ]
        );

        $fieldset->addField(
            'limit_shipments', 'select',
            [
                'label'  => __('Limit Access To Shipments'),
                'name'   => 'amrolepermissions[limit_shipments]',
                'values' => $this->optionList->toOptionArray(),
            ]
        );

        $fieldset->addField(
            'limit_memos', 'select',
            [
                'label'  => __('Limit Access To Credit Memos'),
                'name'   => 'amrolepermissions[limit_memos]',
                'values' => $this->optionList->toOptionArray(),
            ]
        );

        $form->setValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
