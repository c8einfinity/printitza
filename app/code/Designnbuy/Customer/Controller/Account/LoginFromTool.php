<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Customer\Controller\Account;

use Magento\Customer\Model\Account\Redirect as AccountRedirect;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\Exception\EmailNotConfirmedException;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\State\UserLockedException;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\Action;
/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class LoginFromTool extends Action
{
    /** @var AccountManagementInterface */
    protected $customerAccountManagement;

    /** @var Validator */
    protected $formKeyValidator;

    /**
     * @var AccountRedirect
     */
    protected $accountRedirect;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    private $cookieMetadataFactory;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\PhpCookieManager
     */
    private $cookieMetadataManager;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var FormKey
     */
    protected $formKey;


    /**
     * @param Context $context
     * @param Session $customerSession
     * @param AccountManagementInterface $customerAccountManagement
     * @param CustomerUrl $customerHelperData
     * @param Validator $formKeyValidator
     * @param AccountRedirect $accountRedirect
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        AccountManagementInterface $customerAccountManagement,
        CustomerUrl $customerHelperData,
        Validator $formKeyValidator,
        AccountRedirect $accountRedirect,
        JsonFactory $resultJsonFactory,
        \Magento\Framework\Data\Form\FormKey $formKey
    ) {
        $this->session = $customerSession;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->customerUrl = $customerHelperData;
        $this->formKeyValidator = $formKeyValidator;
        $this->accountRedirect = $accountRedirect;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->formKey = $formKey;
        parent::__construct($context);
    }

    /**
     * Get scope config
     *
     * @return ScopeConfigInterface
     * @deprecated
     */
    private function getScopeConfig()
    {
        if (!($this->scopeConfig instanceof \Magento\Framework\App\Config\ScopeConfigInterface)) {
            return \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Magento\Framework\App\Config\ScopeConfigInterface::class
            );
        } else {
            return $this->scopeConfig;
        }
    }

    /**
     * Retrieve cookie manager
     *
     * @deprecated
     * @return \Magento\Framework\Stdlib\Cookie\PhpCookieManager
     */
    private function getCookieManager()
    {
        if (!$this->cookieMetadataManager) {
            $this->cookieMetadataManager = \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Magento\Framework\Stdlib\Cookie\PhpCookieManager::class
            );
        }
        return $this->cookieMetadataManager;
    }

    /**
     * Retrieve cookie metadata factory
     *
     * @deprecated
     * @return \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    private function getCookieMetadataFactory()
    {
        if (!$this->cookieMetadataFactory) {
            $this->cookieMetadataFactory = \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory::class
            );
        }
        return $this->cookieMetadataFactory;
    }

    /**
     * Login post action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        $response = [
            'error' => true,
            'status' => ''
        ];
        $message = '';
        $status = true;


        $login = $this->getRequest()->getPost();
        if (!empty($login['email_id']) && !empty($login['password'])) {
            try {
                $customer = $this->customerAccountManagement->authenticate($login['email_id'], $login['password']);
                $this->session->setCustomerDataAsLoggedIn($customer);
                $this->session->regenerateId();
                if ($this->getCookieManager()->getCookie('mage-cache-sessid')) {
                    $metadata = $this->getCookieMetadataFactory()->createCookieMetadata();
                    $metadata->setPath('/');
                    $this->getCookieManager()->deleteCookie('mage-cache-sessid', $metadata);
                }
                $response['id'] = $this->session->getCustomerId();
                $response['user_name'] = $this->session->getCustomer()->getName();
                $response['status'] = 'true';
                //$xmlString['form_key'] = Mage::getSingleton('core/session')->getFormKey();
            } catch (EmailNotConfirmedException $e) {
                $value = $this->customerUrl->getEmailConfirmationUrl($login['email_id']);
                $message = __(
                    'This account is not confirmed. <a href="%1">Click here</a> to resend confirmation email.',
                    $value
                );
                //$this->messageManager->addError($message);
                $this->session->setUsername($login['email_id']);
                $response['error'] = $message;
                $response['status'] = 'false';
            } catch (UserLockedException $e) {
                $message = __(
                    'The account is locked. Please wait and try again or contact %1.',
                    $this->getScopeConfig()->getValue('contact/email/recipient_email')
                );
                //$this->messageManager->addError($message);
                $this->session->setUsername($login['email_id']);
                $response['error'] = $message;
                $response['status'] = 'false';
            } catch (AuthenticationException $e) {
                $message = __('Invalid login or password.');
                //$this->messageManager->addError($message);
                $this->session->setUsername($login['email_id']);
                $response['error'] = $message;
                $response['status'] = 'false';
            } catch (LocalizedException $e) {
                $message = $e->getMessage();
                //$this->messageManager->addError($message);
                $this->session->setUsername($login['email_id']);
                $response['error'] = $message;
                $response['status'] = 'false';
            } catch (\Exception $e) {
                // PA DSS violation: throwing or logging an exception here can disclose customer password
                /*$this->messageManager->addError(
                    __('An unspecified error occurred. Please contact us for assistance.')
                );*/
                $message = __('An unspecified error occurred. Please contact us for assistance.');
                $response['error'] = $message;
                $response['status'] = 'false';
            }
        } else {
            //$this->messageManager->addError(__('A login and a password are required.'));
            $message = __('A login and a password are required.');
            $response['error'] = $message;
            $response['status'] = 'false';
        }
        $formKey = $this->getFormKey();
        $this->formKey->set($formKey);
        $response['form_key'] = $formKey;
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($response);

    }

    /**
     * get form key
     *
     * @return string
     */
    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }
}
