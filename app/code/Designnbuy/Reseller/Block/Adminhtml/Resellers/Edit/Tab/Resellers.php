<?php
namespace Designnbuy\Reseller\Block\Adminhtml\Resellers\Edit\Tab;

class Resellers extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    protected $_systemStore;
    protected $_objectManager;
    protected $authSession;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Backend\Model\Auth\Session $authSession,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->authSession       = $authSession;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    protected function _prepareForm()
    {
        $resellerData = '';
        $model = $this->_coreRegistry->registry('resellers');
        $isElementRequird = true;
        $isCommsionDisable = false;
        if(count($model->getData())) {
            $isElementRequird = false;
            $resellerData = $model->getData();
            $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $website = $this->_objectManager->create('Magento\Store\Model\Website')->load($model->getWebsiteId());

            $websiteData = $website->getData();
            $resellerData = array_merge($resellerData,$websiteData);

            $adminUser = $this->_objectManager->create('Magento\User\Model\User')->load($model->getUserId());
            $adminData = $adminUser->getData();
            unset($adminData['password']);
            $resellerData = array_merge($resellerData,$adminData);

            $user = $this->authSession->getUser();
            if($user->getId() == $model->getUserId())$isCommsionDisable = true;

            $stores = $website->getStoreIds();
            $storeId = reset($stores);
            $resellerData['store_id'] = $storeId;
            $logoName =  $this->_scopeConfig->getValue(
                'design/header/logo_src',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,$storeId
            );
            $logo = false;
            if($logoName){
                $mediaUrl = $this->_storeManager->getStore($storeId)->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
                $logo = $mediaUrl.'logo/'.$logoName;
                $resellerData['header_logo_src'] = $logo;
            }
        }

        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('page_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Reseller Information')]);

        if ($model->getId()) {
            $fieldset->addField('reseller_id', 'hidden', ['name' => 'reseller_id']);
            $fieldset->addField('store_id', 'hidden', ['name' => 'store_id']);
        }

        if ($model->getId()) {
            $fieldset->addField(
                'name',
                'text',
                [
                    'name' => 'name',
                    'label' => __('Website Name'),
                    'title' => __('Website Name'),
                    'required' => true,
                    //'disabled' => true,
                    'readonly' => true,
                ]
            );
        }else{
            $fieldset->addField(
                'store_code',
                'text',
                [
                    'name' => 'store_code',
                    'label' => __('Store Code'),
                    'title' => __('Store Code'),
                    'required' => true,
                    'class' => 'no-whitespace letters-only',
                    'disabled' => false,
                ]
            );
        }

        $fieldset->addField(
            'header_logo_src',
            'image',
            [
                'name' => 'header_logo_src',
                'label' => __('Website Logo'),
                'title' => __('Website Logo'),
                'required' => true,
                'disabled' => false,
                'note' => 'Allowed file types: png, gif, jpg, jpeg.'
            ]
        );
        $fieldset->addField(
            'firstname',
            'text',
            [
                'name' => 'firstname',
                'label' => __('First Name'),
                'title' => __('First Name'),
                'required' => true,
                'disabled' => false,
            ]
        );

        $fieldset->addField(
            'lastname',
            'text',
            [
                'name' => 'lastname',
                'label' => __('Last Name'),
                'title' => __('Last Name'),
                'required' => true,
                'disabled' => false,
            ]
        );

        $fieldset->addField(
            'email',
            'text',
            [
                'name' => 'email',
                'label' => __('Email'),
                'title' => __('Email'),
                'class' => 'validate-email',
                'required' => true,
            ]
        );

        $fieldset->addField(
            'username',
            'text',
            [
                'name' => 'username',
                'label' => __('User Name'),
                'title' => __('User Name'),
                'required' => true,
            ]
        );

        /*$fieldset->addField(
            'commission',
            'text',
            [
                'label' => __('Commission (%)'),
                'title' => __('Commission'),
                'name' => 'commission',
                'class' => 'validate-digits',
                'required' => true,
                'disabled' => $isCommsionDisable,
            ]
        );*/

        $fieldset->addField(
            'company_registration_number',
            'text',
            [
                'name' => 'company_registration_number',
                'label' => __('Company Registration Number'),
                'title' => __('Company Registration Number'),
                'required' => true,
                'disabled' => false,
            ]
        );

        $fieldset->addField(
            'password',
            'password',
            [
                'name' => 'password',
                'label' => __('New Password'),
                'title' => __('New Password'),
                'class' => 'validate-admin-password',
                'required' => $isElementRequird
            ]
        );

        $fieldset->addField(
            'password_confirmation',
            'password',
            [
                'name' => 'password_confirmation',
                'label' => __('Confirmation Password'),
                'title' => __('Confirmation Password'),
                'required' => $isElementRequird
            ]
        );

        /*$fieldset->addField(
            'is_active',
            'select',
            [
                'label' => __('Account Status'),
                'title' => __('Account Status'),
                'name' => 'is_active',
                'required' => true,
                'options' => $this->status(),
                'disabled' => $isCommsionDisable,
            ]
        );*/

        $form->setValues($resellerData);
        $this->setForm($form);

        return parent::_prepareForm();
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
}