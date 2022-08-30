<?php
/**
 * Copyright © 2019 Designnbuy WebToPrint Pvt. Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 * ALWAYS DO BETTER @a
 */
namespace Drc\Storepickup\Block\Adminhtml\Holidays\Edit\Tab;

class Holidays extends \Magento\Backend\Block\Widget\Form\Generic 
implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    
    protected $_countryFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Drc\Storepickup\Model\Config\Source\HolidayStatus $holidayStatus,        
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_holidayStatus = $holidayStatus;
        parent::__construct($context, $registry, $formFactory, $data);
    }
 
    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Drc\Storepickup\Model\Holidays $holidays */
        $holidays = $this->_coreRegistry->registry('drc_storepickup_holidays');
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('holidays');
        $form->setFieldNameSuffix('holidays');
        
        $yesno = [['value' => '1', 'label' => __('Enabled')], ['value' => '0', 'label' => __('Disabled')]];
        $holidayType = [['value' => 'single', 'label' => __('Single')], ['value' => 'each_year', 'label' => __('Each Year')]];
        
        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => __('Holidays Information'),
                'class'  => 'fieldset-wide'
            ]
        );        
        if ($holidays->getEntityId()) {
            $fieldset->addField(
                'entity_id',
                'hidden',
                ['name' => 'entity_id']
            );
        }
        
        $fieldset->addField(
            'title',
            'text',
            [
                'name'  => 'title',
                'label' => __('Holiday Title'),
                'title' => __('Holiday Title'),
                'required' => true,
            ]
        );

        $fieldset->addField(
            'from_date',
            'date',
            [
                'name'  => 'from_date',
                'label' => __('Holilday From Date'),
                'title' => __('Holilday From Date'),
                'date_format' => \Magento\Framework\Stdlib\DateTime::DATE_INTERNAL_FORMAT, 
                'required' => true,
            ]
        );
        $fieldset->addField(
            'to_date',
            'date',
            [
                'name'  => 'to_date',
                'label' => __('Holilday To Date'),
                'title' => __('Holilday To Date'),
                'date_format' => \Magento\Framework\Stdlib\DateTime::DATE_INTERNAL_FORMAT,                
                'required' => true,
            ]
        );
        
        $fieldset->addField(
            'is_enable',
            'select',
            [
                'name' => 'is_enable',
                'label' => __('Status'),
                'title' => __('Status'),
                'values' => $this->_holidayStatus->toArray()
            ]
        );

        $fieldset->addField(
            'holiday_type',
            'select',
            [
                'name' => 'holiday_type',
                'label' => __('Holilday Type'),
                'title' => __('Holilday Type'),
                'values' => $holidayType
            ]
        );

        $fieldset->addField(
           'store_ids',
           'multiselect',
           [
             'name'     => 'store_ids[]',
             'label'    => __('Store Views'),
             'title'    => __('Store Views'),
             'required' => true,
             'values'   => $this->_systemStore->getStoreValuesForForm(false, true),
           ]
        );


        $holidaysData = $this->_session->getData('drc_storepickup_holidays_data', true);
        $holidaysData = $holidays->getData();
        /*if ($holidaysData) {
            $holidaysData->addData($holidaysData);
        } else {
            if (!$holidaysData->getEntityId()) {
                $holidaysData->addData($holidaysData->getDefaultValues());
            }
        }*/
        //$form->addValues($holidaysData->getData());
        $form->addValues($holidaysData);
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Holidays');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}
