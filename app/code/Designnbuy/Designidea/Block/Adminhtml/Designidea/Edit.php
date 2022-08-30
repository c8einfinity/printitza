<?php

namespace Designnbuy\Designidea\Block\Adminhtml\Designidea;

/**
 * Designidea block edit form container.
 * @category Designnbuy
 * @package  Designnbuy_Designidea
 * @module   Designidea
 * @author   Designnbuy Developer
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry.
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @param Context $context
     * @param array   $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        
        parent::__construct($context, $data);
    }
    protected function _construct()
    {
        $this->_objectId   = 'id';
        $this->_blockGroup = 'Designnbuy_Designidea';
        $this->_controller = 'adminhtml_designidea';

        parent::_construct();

        $this->buttonList->update('save', 'label', __('Save Editable Artwork'));
        $this->buttonList->update('delete', 'label', __('Delete'));

        $this->buttonList->add(
            'save_and_continue',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => [
                            'event' => 'saveAndContinueEdit',
                            'target' => '#edit_form'
                        ],
                    ],
                ],
            ],
            10
        );

    }

    public function getCurrentDesignidea()
    {
        return $this->_coreRegistry->registry('designidea');
    }
    /**
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', ['_current' => true, 'back' => null]);
    }
    /**
     * Retrieve the save and continue edit Url.
     *
     * @return string
     */
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl(
            '*/*/save',
            ['_current' => true, 'back' => 'edit', 'tab' => '{{tab_id}}']
        );
    }

    /**
     * get create banner url.
     *
     * @return string
     */
    public function getCreateBannerUrl()
    {
        return $this->getUrl('*/banner/new', ['current_designidea_id' => $this->getCurrentDesignidea()->getId()]);
    }
}
