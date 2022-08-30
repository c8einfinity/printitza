<?php
namespace Designnbuy\Customer\Block\Adminhtml\Group;

/**
 * Class Edit
 * @package Designnbuy\Productattach\Block\Adminhtml\Productattach
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize cms page edit block
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Designnbuy_Customer';
        $this->_controller = 'adminhtml_group';

        parent::_construct();

        
        $this->buttonList->update('save', 'label', __('Save Customer Group'));
        $this->buttonList->add(
            'saveandcontinue',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
                    ],
                ]
            ],
            -100
        );
        
        $this->buttonList->add(
            'back_button',
            [
                'label' => __('Back'),
                'onclick' => 'setLocation(\'' . $this->getUrl('customer/group/index') . '\')',
                'class' => 'back'
            ],
            -1
        );

        $this->buttonList->remove('delete');
        $this->buttonList->remove('back');
        
    }
    public function getSaveUrl()
    {
        return $this->getUrl('customer/group/save');
    }
}
