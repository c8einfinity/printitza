<?php


namespace Designnbuy\Vendor\Controller\Adminhtml\User;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\State\UserLockedException;
use Magento\Security\Model\SecurityCookie;
class Save extends \Magento\Backend\App\Action
{

    protected $dataPersistor;

    /**
     * User model factory
     *
     * @var \Magento\User\Model\UserFactory
     */
    protected $_userFactory;


    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;


    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     * @param \Magento\User\Model\UserFactory $userFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Magento\User\Model\UserFactory $userFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->_userFactory = $userFactory;
        $this->_scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();

        if ($data) {

            $id = $this->getRequest()->getParam('id');

            $vendorUserModel = $this->_objectManager->create('Designnbuy\Vendor\Model\User')->load($id);
            if (!$vendorUserModel->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This vendor no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }
            /** Assign Prducts to Vendor */
            $links = isset($data['data']['designnbuy_vendor_product_listing']) ? $data['data']['designnbuy_vendor_product_listing'] : "";
            $productIds = array();
            if (is_array($links) && $vendorUserModel->getUserId() != "") {
                foreach ($links as $key => $value) {
                    $productIds[] = $value['entity_id'];
                }
                $vendorId = $vendorUserModel->getUserId();
                $storeId = $this->_storeManager->getStore(\Magento\Store\Model\Store::ADMIN_CODE)->getId();
                $this->updateProductAttributeData($productIds, $vendorId, $storeId);
            }
            try {
                $user = $this->_createAdminUser($vendorUserModel, $data);

                if($user){
                    $vendorUserData = [];
                    $vendorUserData['user_id'] = $user->getUserId();
                    if($id){
                        $vendorUserData['id'] = $id;
                    }
                    $vendorUserData['commission_percentage'] = $data['commission_percentage'];
                    //$vendorUserData['role_id'] = $data['role_id'];
                    $vendorUserData['notify_user'] = $data['notify_user'];
                    $vendorUserData['folder_name'] = $data['folder_name'];
                    $vendorUserData['remote_host'] = $data['remote_host'];
                    $vendorUserData['ftp_port'] = $data['ftp_port'];
                    $vendorUserData['ftp_username'] = $data['ftp_username'];
                    $vendorUserData['ftp_password'] = $data['ftp_password'];
                    $vendorUserData['ftp_path'] = $data['ftp_path'];
                    $vendorUserData['connection_timeout'] = $data['connection_timeout'];
                    $vendorUserData['passive_ftp'] = $data['passive_ftp'];
                    $vendorUserData['status'] = $user->getIsActive();

                    $vendorUserModel->setData($vendorUserData);
                    $vendorUserModel->save();
                    $this->messageManager->addSuccessMessage(__('You saved the vendor.'));
                    $this->dataPersistor->clear('designnbuy_vendor_user');
                }
        
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $vendorUserModel->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                $this->dataPersistor->set('designnbuy_vendor_user', $data);
                $arguments = ['_current' => true, 'active_tab' => ''];
                $resultRedirect->setPath('*/*/edit', $arguments);
            } catch (\Exception $e) {
                echo $e->getMessage(); exit;
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the User.'));
            }
        
            $this->dataPersistor->set('designnbuy_vendor_user', $data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    protected function _createAdminUser($vendorUser, $data){
        $userId = (int)$data['user_id'];

        /** @var $model \Magento\User\Model\User */
        $model = $this->_userFactory->create()->load($userId);

        $model->setData($this->_getAdminUserData($data));

        $defaultRoleId = $this->_scopeConfig->getValue(
            'dnbvendor/settings/default_role',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $model->setRoleId($defaultRoleId);
        if($data['user_id'] == ''){
            $model->setId(null);
        }
        /** @var $currentUser \Magento\User\Model\User */
        $currentUser = $this->_objectManager->get('Magento\Backend\Model\Auth\Session')->getUser();


        /** Before updating admin user data, ensure that password of current admin user is entered and is correct */
        $currentUserPasswordField = \Magento\User\Block\User\Edit\Tab\Main::CURRENT_USER_PASSWORD_FIELD;

        $isCurrentUserPasswordValid = isset($data[$currentUserPasswordField])
            && !empty($data[$currentUserPasswordField]) && is_string($data[$currentUserPasswordField]);
        try {
            if (!($isCurrentUserPasswordValid)) {
                throw new AuthenticationException(__('You have entered an invalid password for current user.'));
            }
            $currentUser->performIdentityCheck($data[$currentUserPasswordField]);
            $model->save();
            //$model->sendNotificationEmailsIfRequired();
            return $model;
        } catch (UserLockedException $e) {
            $this->_auth->logout();
            $this->getSecurityCookie()->setLogoutReasonCookie(
                \Magento\Security\Model\AdminSessionsManager::LOGOUT_REASON_USER_LOCKED
            );
            $this->_redirect('*/*/');
        } catch (\Magento\Framework\Exception\AuthenticationException $e) {
            $this->messageManager->addError(__('You have entered an invalid password for current user.'));
            $this->redirectToEdit($vendorUser, $data);
        } catch (\Magento\Framework\Validator\Exception $e) {
            $messages = $e->getMessages();
            $this->messageManager->addMessages($messages);
            $this->redirectToEdit($vendorUser, $data);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            if ($e->getMessage()) {
                $this->messageManager->addError($e->getMessage());
            }
            $this->redirectToEdit($vendorUser, $data);
        }
    }
    /**
     * @param \Magento\User\Model\User $model
     * @param array $data
     * @return void
     */
    protected function redirectToEdit(\Designnbuy\Vendor\Model\User $model, array $data)
    {
        $this->_getSession()->setUserData($data);
        $arguments = $model->getId() ? ['id' => $model->getId()] : [];
        $arguments = array_merge($arguments, ['_current' => true, 'active_tab' => '']);
        $this->_redirect('*/*/edit', $arguments);
    }
    /**
     * Retrieve well-formed admin user data from the form input
     *
     * @param array $data
     * @return array
     */
    protected function _getAdminUserData(array $data)
    {
        if (isset($data['password']) && $data['password'] === '') {
            unset($data['password']);
        }
        if (!isset($data['password'])
            && isset($data['password_confirmation'])
            && $data['password_confirmation'] === ''
        ) {
            unset($data['password_confirmation']);
        }
        return $data;
    }

    public function updateProductAttributeData($productIds, $vendorId, $storeId){
        $productCollection = $this->productCollection->create();
        $productCollection->addFieldToFilter('entity_id', array('in' => $productIds));
        $productCollection->addAttributeToSelect('*');

        foreach ($productCollection as $key => $_product) {
            if($_product->getId() != ''):
                $updateAttributesData = ['vendor_id' => $vendorId];
                $this->_objectManager->get('Magento\Catalog\Model\Product\Action')->updateAttributes([$_product->getId()], $updateAttributesData, $storeId);
            endif;
        }
    }

}
