<?php
namespace Designnbuy\Reseller\Block\Adminhtml\Resellers\Edit\Tab;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Info extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    protected $_systemStore;
    protected $_objectManager;
    protected $authSession;
    protected $_currency;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Config\Model\Config\Source\Locale\Currency $currency,
        ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\Website $website,
        \Designnbuy\Reseller\Helper\Data $resellerHelper,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->authSession  = $authSession;
        $this->_currency = $currency;
        $this->website = $website;
        $this->scopeConfig = $scopeConfig;
        $this->resellerHelper = $resellerHelper;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /*{{CedPrepareForm}}*/   
        $form = $this->_formFactory->create();
        $model = $this->_coreRegistry->registry('resellers');

        $fieldset = $form->addFieldset(
            'reseller_currency_heading',
            ['legend' => __('E-Commerce Information'), 'class' => 'store-scope']
        );

        $fieldset->addField('reseller_currency', 'select', array(
            'name' => 'reseller_currency',
            'label' => __('Default Currency'),
            'title' => __('Default Currency'),
            'required' => false,
            'class' => 'input-select',
            'values' => $this->_currency->toOptionArray(),
            'value' => $this->getCurrencyCode(),
        ));     
        
        $fieldset->addField('bank_detail', 'text', array(
            'name' => 'bank_detail',
            'label' => __('Bank Detail'),
            'title' => __('Bank Detail'),
            'required' => false,
            'class' => 'input-text',
            'value' => $model->getBankDetail(),
        ));

        $fieldset->addField('vat_number', 'text', array(
            'name' => 'vat_number',
            'label' => __('Vat Number'),
            'title' => __('Vat Number'),
            'required' => false,
            'class' => 'input-text',
            'value' => $model->getVatNumber(),
        ));

        $user = $this->authSession->getUser();
        $reseller = $this->resellerHelper->isResellerUser($user->getId());
        
        if(!$reseller):
            $fieldset->addField('commission_type', 'select', array(
                'name' => 'commission_type',
                'label' => __('Commission Type'),
                'title' => __('Commission Type'),
                'required' => false,
                'values' => $this->getCommissionType(),
                'class' => 'input-text',
                'style'     => 'width:250px;',
                'value' => $this->getCommissionTypeValue(),
            ));
           $fieldset->addField('product_commission', 'text', array(
	            'name' => 'product_commission',
	            'label' => __('Product Commission'),
	            'title' => __('Product Commission'),
	            'required' => true,                
	            'class' => 'input-text',
                'style'     => 'width:250px;',
	            'value' => $model->getProductCommission(),
                
	        ));
        else:
            $fieldset->addField('commission_type', 'select', array(
                'name' => 'commission_type',
                'label' => __('Commission Type'),
                'title' => __('Commission Type'),
                'required' => false,
                'values' => $this->getCommissionType(),
                'class' => 'input-text',
                'style'     => 'width:250px;',
                'value' => $this->getCommissionTypeValue(),
                'readonly' => true,
                'disabled' => true,
            ));

           $fieldset->addField('product_commission', 'text', array(
                'name' => 'product_commission',
                'label' => __('Product Commission'),
                'title' => __('Product Commission'),
                'required' => true,                
                'class' => 'input-text',
                'style'     => 'width:250px;',
                'value' => $model->getProductCommission(),
                'readonly' => true,
                'disabled' => true,
                'note' => 'Reseller has been applied above commission to all products display on store.',
            ));
        endif;

        $this->setForm($form);
        
        return parent::_prepareForm();
    }
    
    public function getCurrencyCode() {
        $model = $this->_coreRegistry->registry('resellers');
        $website = $this->website->load($model->getWebsiteId());
        $scope = \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITES;
        return $this->scopeConfig->getValue('currency/options/default', $scope , $website->getName());
    }

    public function getTabLabel()
    {
        return __('Reseller Information');
    }

    public function getTabTitle()
    {
        return __('Reseller Information');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }

    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    protected function status(){
        return ['1'=>__('Active'), '0' =>__('Inactive')];
    }

    protected function getCommissionType(){
        return ['1'=>__('Percentage'), '2' =>__('Fixed')];
    }

    public function getCommissionTypeValue() {
        $model = $this->_coreRegistry->registry('resellers');
        return $model->getCommissionType();
    }
}
