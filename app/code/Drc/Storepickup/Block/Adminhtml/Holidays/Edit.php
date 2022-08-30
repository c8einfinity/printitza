<?php
/**
  * Copyright © 2019 Designnbuy WebToPrint Pvt. Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 * ALWAYS DO BETTER @a
 */
namespace Drc\Storepickup\Block\Adminhtml\Holidays;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * constructor
     *
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    ) {
        $this->coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize Holidays edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'entity_id';
        $this->_blockGroup = 'Drc_Storepickup';
        $this->_controller = 'adminhtml_holidays';
        parent::_construct();
        $this->buttonList->update('save', 'label', __('Save Holidays'));
        $this->buttonList->add(
            'save-and-continue',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => [
                            'event' => 'saveAndContinueEdit',
                            'target' => '#edit_form'
                        ]
                    ]
                ]
            ],
            -100
        );
        $this->buttonList->update('delete', 'label', __('Delete Holidays'));
    }
    /**
     * Retrieve text for header element depending on loaded Holidays
     *
     * @return string
     */
    public function getHeaderText()
    {
        /** @var \Drc\Storepickup\Model\Holidays $holidays */
        $holidays = $this->coreRegistry->registry('drc_storepickup_holidays');
        if ($holidays->getId()) {
            return __("Edit Holidays '%1'", $this->escapeHtml($holidays->getStore_title()));
        }
        return __('New Holidays');
    }
}
