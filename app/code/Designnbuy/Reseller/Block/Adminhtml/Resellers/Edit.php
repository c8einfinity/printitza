<?php
namespace Designnbuy\Reseller\Block\Adminhtml\Resellers;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Designnbuy\Reseller\Helper\Data
     */
    protected $_helper;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Model\Config $salesConfig
     * @param \Magento\Sales\Helper\Reorder $reorderHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \Designnbuy\Reseller\Helper\Data $helper,
        array $data = []
    ) {
        $this->_helper  = $helper;
        $this->_coreRegistry  = $registry;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Designnbuy_Reseller';
        $this->_controller = 'adminhtml_resellers';

        parent::_construct();

        $this->buttonList->update('save', 'label', __('Save Reseller'));
        $this->buttonList->update('delete', 'label', __('Delete Reseller'));

        $this->buttonList->add(
            'saveandcontinue',
            array(
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => array(
                    'mage-init' => array('button' => array('event' => 'saveAndContinueEdit', 'target' => '#edit_form'))
                )
            ),
            -100
        );
        
        if ($this->_coreRegistry->registry('resellers')->getResellerId() && $this->getRequest()->getFullActionName() && $this->getRequest()->getFullActionName() != "designnbuy_reseller_resellers_importproducts") {

            $this->buttonList->add(
                'updatecommission',
                array(
                    'label'        => __('Add Product Commission'),
                    'onclick'    => 'updateCommission(\'' . $this->getUrl('*/*/UpdateProductCommission', ['id' => $this->_coreRegistry->registry('resellers')->getResellerId()]) . '\')',
                    'class'        => 'save',
                ), -100);


            $resellerUserId = $this->_coreRegistry->registry('resellers')->getUserId();
            //$resellerStatus = $this->_coreRegistry->registry('resellers')->getStatus();
            $userStatus = $this->_helper->getResellerUserStatus($resellerUserId);
            if($userStatus == false){
                $this->buttonList->add(
                    'inactive_user',
                    [
                        'label' => __('Active Reseller'),
                        'class' => 'save primary',
                        'style' => 'background-color:green',
                        'onclick' => 'setLocation(\'' . $this->getUrl('*/*/InActiveUser', ['id' => $this->_coreRegistry->registry('resellers')->getResellerId(), 'type' => 'activate']) . '\')',
                    ],
                    -100
                );
            }else{
                $this->buttonList->add(
                    'inactive_user',
                    [
                        'label' => __('In Active Reseller'),
                        'class' => 'save primary',
                        'style' => 'background-color:red',
                        'onclick' => 'setLocation(\'' . $this->getUrl('*/*/InActiveUser', ['id' => $this->_coreRegistry->registry('resellers')->getResellerId()]) . '\')',
                    ],
                    -100
                );
            }

            /* $this->buttonList->add(
                'apply_theme',
                [
                    'label' => __('Apply Theme'),
                    'class' => 'save primary', */
                    //'onclick' => 'setLocation(\'' . $this->getUrl('*/*/ApplyTheme', ['id' => $this->_coreRegistry->registry('resellers')->getResellerId()]) . '\')',
                /* ],
                -100
            ); */
        }
    }

    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('resellers')->getId()) {
            return __("Edit Reseller");
        } else {
            return __('New Reseller');
        }
    }

    /**
     * Prepare layout
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        $this->_formScripts[] = "                        
            function updateCommission(url){
                $('edit_form').action = url;
                $('edit_form').submit(url);                
            }
        ";
        return parent::_prepareLayout();
    }
}
