<?php

namespace Unific\Connector\Block\Adminhtml\Log\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;

class Info extends Generic
{
    /**
     * Retrieve log object
     *
     * @return \Unific\Connector\Model\Log
     */
    public function getModel()
    {
        return $this->_coreRegistry->registry('_unific_connector_log');
    }

    /**
     * Prepare form fields
     *
     * @SuppressWarnings(MEQP2.PHP.ProtectedClassMember.FoundProtected)
     * @return \Magento\Backend\Block\Widget\Form
     */
    protected function _prepareForm()
    {
        $model = $this->getModel();

        $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form_info', 'action' => $this->getData('action'), 'method' => 'post']]
        );

        $form->setFieldContainerIdPrefix('info_');
        $form->addFieldNameSuffix('info');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('General Information'), 'class' => 'fieldset-wide']
        );

        $fieldset->addField(
            'date_created',
            'text',
            [
                'name' => 'date_created',
                'label' => __('Log Created'),
                'title' => __('Log Created')
            ]
        );

        $fieldset->addField(
            'request_guid',
            'text',
            [
                'name' => 'request_guid',
                'label' => __('Request GUID'),
                'title' => __('Request GUID')
            ]
        );

        $fieldset->addField(
            'message_status',
            'text',
            [
                'name' => 'message_status',
                'label' => __('Webhook Status'),
                'title' => __('Webhook Status')
            ]
        );

        $responseFieldset = $form->addFieldset(
            'response_fieldset',
            ['legend' => __('Response'), 'class' => 'fieldset-wide']
        );

        $responseFieldset->addField(
            'response_http_code',
            'text',
            [
                'name' => 'response_http_code',
                'label' => __('Response HTTP Code'),
                'title' => __('Response HTTP Code')
            ]
        );

        $responseFieldset->addField(
            'response_message',
            'text',
            [
                'name' => 'response_message',
                'label' => __('Response Message'),
                'title' => __('Response Message')
            ]
        );

        $requestFieldset = $form->addFieldset(
            'request_fieldset',
            ['legend' => __('Request'), 'class' => 'fieldset-wide']
        );

        $requestFieldset->addField(
            'request_url',
            'text',
            [
                'name' => 'request_url',
                'label' => __('Request URL'),
                'title' => __('Request URL')
            ]
        );

        $requestFieldset->addField(
            'request_type',
            'text',
            [
                'name' => 'request_type',
                'label' => __('Request URL'),
                'title' => __('Request URL')
            ]
        );

        $requestFieldset->addField(
            'request_headers',
            'textarea',
            [
                'name' => 'request_headers',
                'label' => __('Request Headers'),
                'title' => __('Request Headers')
            ]
        );

        $requestFieldset->addField(
            'request_message',
            'textarea',
            [
                'name' => 'request_message',
                'label' => __('Request Content'),
                'title' => __('Request Content')
            ]
        );

        $data = $model->getData();
        $data['request_headers'] = var_export(json_decode($data['request_headers'], true), true);
        $data['request_message'] = var_export(json_decode($data['request_message'], true), true);
        $form->setValues($data);

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
