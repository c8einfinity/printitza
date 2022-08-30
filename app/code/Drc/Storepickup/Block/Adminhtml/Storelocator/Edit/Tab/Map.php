<?php
/**
 * @author Drc Systems India Pvt Ltd.
 */

namespace Drc\Storepickup\Block\Adminhtml\Storelocator\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

class Map extends Generic implements TabInterface
{

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Google Map');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Google Map');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('drc_storepickup_storelocator');
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('storelocator_');
        $form->setFieldNameSuffix('storelocator');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Google Map')]);
        
        $fieldset->addField(
            'latitude',
            'text',
            ['name' => 'latitude', 'label' => __('Latitude'), 'title' => __('Latitude'), 'required' => true]
        );
        $fieldset->addField(
            'longitude',
            'text',
            ['name' => 'longitude', 'label' => __('Longitude'), 'title' => __('Longitude'), 'required' => true]
        );
       
        /***** Render *****/
        $field = $fieldset->addField(
            'customfield',
            'text',
            ['name'     => 'customfield','title'    => __('Custom Field'),]
        );
        
        $renderer = $this->getLayout()
        ->createBlock('Drc\Storepickup\Block\Adminhtml\Storelocator\Edit\Tab\Renderer\Customfield');
        $field->setRenderer($renderer);
        /***** Render *****/
        
        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
