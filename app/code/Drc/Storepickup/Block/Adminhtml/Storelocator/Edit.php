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
namespace Drc\Storepickup\Block\Adminhtml\Storelocator;

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
     * Initialize Storelocator edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'storelocator_id';
        $this->_blockGroup = 'Drc_Storepickup';
        $this->_controller = 'adminhtml_storelocator';
        parent::_construct();
        $this->buttonList->update('save', 'label', __('Save Storelocator'));
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
        $this->buttonList->update('delete', 'label', __('Delete Storelocator'));
    }
    /**
     * Retrieve text for header element depending on loaded Storelocator
     *
     * @return string
     */
    public function getHeaderText()
    {
        /** @var \Drc\Storepickup\Model\Storelocator $storelocator */
        $storelocator = $this->coreRegistry->registry('drc_storepickup_storelocator');
        if ($storelocator->getId()) {
            return __("Edit Storelocator '%1'", $this->escapeHtml($storelocator->getStore_title()));
        }
        return __('New Storelocator');
    }
}
