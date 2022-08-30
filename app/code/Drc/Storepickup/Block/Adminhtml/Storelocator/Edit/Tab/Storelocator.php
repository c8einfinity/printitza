<?php
/**
 * {{Drc}}_{{Storepickup}} extension
 *                     NOTICE OF LICENSE
 *
 *                     This source file is subject to the MIT License
 *                     that is bundled with this package in the file LICENSE.txt.
 *                     It is also available through the world-wide-web at this URL:
 *                     http://opensource.org/licenses/mit-license.php
 *
 *                     @category  {{Drc}}
 *                     @package   {{Drc}}_{{Storepickup}}
 *                     @copyright Copyright (c) {{2016}}
 *                     @license   http://opensource.org/licenses/mit-license.php MIT License
 */
namespace Drc\Storepickup\Block\Adminhtml\Storelocator\Edit\Tab;

class Storelocator extends \Magento\Backend\Block\Widget\Form\Generic 
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
        \Magento\Directory\Model\Config\Source\Country $countryFactory,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_countryFactory = $countryFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }
 
    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Drc\Storepickup\Model\Storelocator $storelocator */
        $storelocator = $this->_coreRegistry->registry('drc_storepickup_storelocator');
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('storelocator_');
        $form->setFieldNameSuffix('storelocator');
        
        $yesno = [['value' => 'Enabled', 'label' => __('Enabled')], ['value' => 'Disabled', 'label' => __('Disabled')]];
        
        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => __('Storelocator Information'),
                'class'  => 'fieldset-wide'
            ]
        );
        if ($storelocator->getId()) {
            $fieldset->addField(
                'storelocator_id',
                'hidden',
                ['name' => 'storelocator_id']
            );
        }
        $fieldset->addField(
            'store_title',
            'text',
            [
                'name'  => 'store_title',
                'label' => __('Store Title'),
                'title' => __('Store Title'),
                'required' => true,
            ]
        );
        $fieldset->addField(
            'address',
            'text',
            [
                'name'  => 'address',
                'label' => __('Address'),
                'title' => __('Address'),
                'required' => true,
            ]
        );
        $fieldset->addField(
            'city',
            'text',
            [
                'name'  => 'city',
                'label' => __('City'),
                'title' => __('City'),
                'required' => true,
            ]
        );
        $fieldset->addField(
            'state',
            'text',
            [
                'name'  => 'state',
                'label' => __('State'),
                'title' => __('State'),
                'required' => true,
            ]
        );
        $fieldset->addField(
            'pincode',
            'text',
            [
                'name'  => 'pincode',
                'label' => __('Pincode'),
                'title' => __('Pincode'),
                'required' => true,
            ]
        );
        $optionsc=$this->_countryFactory->toOptionArray();
        $country = $fieldset->addField(
            'country',
            'select',
            [
                'name' => 'country',
                'label' => __('Country'),
                'title' => __('Country'),
                'values' => $optionsc,
            ]
        );
        $fieldset->addField(
            'phone',
            'text',
            [
                'name'  => 'phone',
                'label' => __('Phone'),
                'title' => __('Phone'),
                'required' => true,
            ]
        );
        $fieldset->addField(
            'email',
            'text',
            [
                'name'  => 'email',
                'label' => __('Email'),
                'title' => __('Email'),
                'required' => true,
            ]
        );
        $fieldset->addField(
            'image',
            'image',
            [
            'title' => __('Store Image'),
            'label' => __('Store Image'),
            'name' => 'image',
            'note' => 'Allow image type: jpg, jpeg, gif, png',
            ]
        );
        
        $fieldset->addField(
            'is_enable',
            'select',
            [
                'name' => 'is_enable',
                'label' => __('Status'),
                'title' => __('Status'),
                'values' => $yesno
            ]
        );

        $storelocatorData = $this->_session->getData('drc_storepickup_storelocator_data', true);
        if ($storelocatorData) {
            $storelocator->addData($storelocatorData);
        } else {
            if (!$storelocator->getId()) {
                $storelocator->addData($storelocator->getDefaultValues());
            }
        }
        $form->addValues($storelocator->getData());
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
        return __('Storelocator');
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
