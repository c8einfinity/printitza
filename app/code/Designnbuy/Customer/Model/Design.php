<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Customer\Model;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\MailException;

/**
 * Design model
 *
 * @method \Designnbuy\Customer\Model\ResourceModel\Design _getResource()
 * @method \Designnbuy\Customer\Model\ResourceModel\Design getResource()
 * @method int getStoreId()
 * @method $this setStoreId(int $value)
 * @method string getChangeStatusAt()
 * @method $this setChangeStatusAt(string $value)
 * @method int getCustomerId()
 * @method $this setCustomerId(int $value)
 * @method string getDesignEmail()
 * @method $this setDesignEmail(string $value)
 * @method int getDesignStatus()
 * @method $this setDesignStatus(int $value)
 * @method string getDesignConfirmCode()
 * @method $this setDesignConfirmCode(string $value)
 * @method int getDesignId()
 * @method Design setDesignId(int $value)
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 */
class Design extends \Magento\Framework\Model\AbstractModel
{
    const STATUS_SUBSCRIBED = 1;
    const STATUS_NOT_ACTIVE = 2;
    const STATUS_UNSUBSCRIBED = 3;
    const STATUS_UNCONFIRMED = 4;

    const XML_PATH_CONFIRM_EMAIL_TEMPLATE = 'customer/subscription/confirm_email_template';
    const XML_PATH_CONFIRM_EMAIL_IDENTITY = 'customer/subscription/confirm_email_identity';
    const XML_PATH_SUCCESS_EMAIL_TEMPLATE = 'customer/subscription/success_email_template';
    const XML_PATH_SUCCESS_EMAIL_IDENTITY = 'customer/subscription/success_email_identity';
    const XML_PATH_UNSUBSCRIBE_EMAIL_TEMPLATE = 'customer/subscription/un_email_template';
    const XML_PATH_UNSUBSCRIBE_EMAIL_IDENTITY = 'customer/subscription/un_email_identity';
    const XML_PATH_CONFIRMATION_FLAG = 'customer/subscription/confirm';
    const XML_PATH_ALLOW_GUEST_SUBSCRIBE_FLAG = 'customer/subscription/allow_guest_subscribe';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'designnbuy_customer_design';

    /**
     * Parameter name in event
     *
     * In observe method you can use $observer->getEvent()->getObject() in this case
     *
     * @var string
     */
    protected $_eventObject = 'design';

    /**
     * True if data changed
     *
     * @var bool
     */
    protected $_isStatusChanged = false;

    /**
     * Customer data
     *
     * @var \Designnbuy\Customer\Helper\Data
     */
    protected $_customerData = null;

    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Customer session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var AccountManagementInterface
     */
    protected $customerAccountManagement;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Designnbuy\Customer\Helper\Data $customerData
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\Session $customerSession
     * @param CustomerRepositoryInterface $customerRepository
     * @param AccountManagementInterface $customerAccountManagement
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Designnbuy\Customer\Helper\Data $customerData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Session $customerSession,
        CustomerRepositoryInterface $customerRepository,
        AccountManagementInterface $customerAccountManagement,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_customerData = $customerData;
        $this->_scopeConfig = $scopeConfig;
        $this->_transportBuilder = $transportBuilder;
        $this->_storeManager = $storeManager;
        $this->_customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->inlineTranslation = $inlineTranslation;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Designnbuy\Customer\Model\ResourceModel\Design');
    }

    /**
     * Alias for getDesignId()
     *
     * @return int
     */
    public function getId()
    {
        return $this->getDesignId();
    }

    /**
     * Alias for setDesignId()
     *
     * @param int $value
     * @return $this
     */
    public function setId($value)
    {
        return $this->setDesignId($value);
    }

    /**
     * Alias for getDesignConfirmCode()
     *
     * @return string
     */
    public function getCode()
    {
        return $this->getDesignConfirmCode();
    }

    /**
     * Return link for confirmation of subscription
     *
     * @return string
     */
    public function getConfirmationLink()
    {
        return $this->_customerData->getConfirmationUrl($this);
    }

    /**
     * Returns Unsubscribe url
     *
     * @return string
     */
    public function getUnsubscriptionLink()
    {
        return $this->_customerData->getUnsubscribeUrl($this);
    }

    /**
     * Alias for setDesignConfirmCode()
     *
     * @param string $value
     * @return $this
     */
    public function setCode($value)
    {
        return $this->setDesignConfirmCode($value);
    }

    /**
     * Alias for getDesignStatus()
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->getDesignStatus();
    }

    /**
     * Alias for setDesignStatus()
     *
     * @param int $value
     * @return $this
     */
    public function setStatus($value)
    {
        return $this->setDesignStatus($value);
    }

    /**
     * Set the error messages scope for subscription
     *
     * @param boolean $scope
     * @return $this
     */

    public function setMessagesScope($scope)
    {
        $this->getResource()->setMessagesScope($scope);
        return $this;
    }

    /**
     * Alias for getDesignEmail()
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->getDesignEmail();
    }

    /**
     * Alias for setDesignEmail()
     *
     * @param string $value
     * @return $this
     */
    public function setEmail($value)
    {
        return $this->setDesignEmail($value);
    }

    /**
     * Set for status change flag
     *
     * @param boolean $value
     * @return $this
     */
    public function setStatusChanged($value)
    {
        $this->_isStatusChanged = (boolean) $value;
        return $this;
    }

    /**
     * Return status change flag value
     *
     * @return boolean
     */
    public function isStatusChanged()
    {
        return $this->_isStatusChanged;
    }

    /**
     * Return customer subscription status
     *
     * @return bool
     */
    public function isSubscribed()
    {
        if ($this->getId() && $this->getStatus() == self::STATUS_SUBSCRIBED) {
            return true;
        }

        return false;
    }

    /**
     * Load design data from resource model by email
     *
     * @param string $designEmail
     * @return $this
     */
    public function loadByEmail($designEmail)
    {
        $this->addData($this->getResource()->loadByEmail($designEmail));
        return $this;
    }

    /**
     * Load design info by customerId
     *
     * @param int $customerId
     * @return $this
     */
    public function loadByCustomerId($customerId)
    {
        try {
            $customerData = $this->customerRepository->getById($customerId);
            $data = $this->getResource()->loadByCustomerData($customerData);
            $this->addData($data);
            if (!empty($data) && $customerData->getId() && !$this->getCustomerId()) {
                $this->setCustomerId($customerData->getId());
                $this->setDesignConfirmCode($this->randomSequence());
                $this->save();
            }
        } catch (NoSuchEntityException $e) {
        }
        return $this;
    }

    /**
     * Returns string of random chars
     *
     * @param int $length
     * @return string
     */
    public function randomSequence($length = 32)
    {
        $id = '';
        $par = [];
        $char = array_merge(range('a', 'z'), range(0, 9));
        $charLen = count($char) - 1;
        for ($i = 0; $i < $length; $i++) {
            $disc = \Magento\Framework\Math\Random::getRandomNumber(0, $charLen);
            $par[$i] = $char[$disc];
            $id = $id . $char[$disc];
        }
        return $id;
    }

    /**
     * Subscribes by email
     *
     * @param string $email
     * @throws \Exception
     * @return int
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function subscribe($email)
    {
        $this->loadByEmail($email);

        if (!$this->getId()) {
            $this->setDesignConfirmCode($this->randomSequence());
        }

        $isConfirmNeed = $this->_scopeConfig->getValue(
            self::XML_PATH_CONFIRMATION_FLAG,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ) == 1 ? true : false;
        $isOwnSubscribes = false;

        $isSubscribeOwnEmail = $this->_customerSession->isLoggedIn()
            && $this->_customerSession->getCustomerDataObject()->getEmail() == $email;

        if (!$this->getId() || $this->getStatus() == self::STATUS_UNSUBSCRIBED
            || $this->getStatus() == self::STATUS_NOT_ACTIVE
        ) {
            if ($isConfirmNeed === true) {
                // if user subscribes own login email - confirmation is not needed
                $isOwnSubscribes = $isSubscribeOwnEmail;
                if ($isOwnSubscribes == true) {
                    $this->setStatus(self::STATUS_SUBSCRIBED);
                } else {
                    $this->setStatus(self::STATUS_NOT_ACTIVE);
                }
            } else {
                $this->setStatus(self::STATUS_SUBSCRIBED);
            }
            $this->setDesignEmail($email);
        }

        if ($isSubscribeOwnEmail) {
            try {
                $customer = $this->customerRepository->getById($this->_customerSession->getCustomerId());
                $this->setStoreId($customer->getStoreId());
                $this->setCustomerId($customer->getId());
            } catch (NoSuchEntityException $e) {
                $this->setStoreId($this->_storeManager->getStore()->getId());
                $this->setCustomerId(0);
            }
        } else {
            $this->setStoreId($this->_storeManager->getStore()->getId());
            $this->setCustomerId(0);
        }

        $this->setStatusChanged(true);

        try {
            $this->save();
            if ($isConfirmNeed === true
                && $isOwnSubscribes === false
            ) {
                $this->sendConfirmationRequestEmail();
            } else {
                $this->sendConfirmationSuccessEmail();
            }
            return $this->getStatus();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Unsubscribes loaded subscription
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return $this
     */
    public function unsubscribe()
    {
        if ($this->hasCheckCode() && $this->getCode() != $this->getCheckCode()) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('This is an invalid subscription confirmation code.')
            );
        }

        if ($this->getDesignStatus() != self::STATUS_UNSUBSCRIBED) {
            $this->setDesignStatus(self::STATUS_UNSUBSCRIBED)->save();
            $this->sendUnsubscriptionEmail();
        }
        return $this;
    }

    /**
     * Subscribe the customer with the id provided
     *
     * @param int $customerId
     * @return $this
     */
    public function subscribeCustomerById($customerId)
    {
        return $this->_updateCustomerSubscription($customerId, true);
    }

    /**
     * unsubscribe the customer with the id provided
     *
     * @param int $customerId
     * @return $this
     */
    public function unsubscribeCustomerById($customerId)
    {
        return $this->_updateCustomerSubscription($customerId, false);
    }

    /**
     * Update the subscription based on latest information of associated customer.
     *
     * @param int $customerId
     * @return $this
     */
    public function updateSubscription($customerId)
    {
        $this->loadByCustomerId($customerId);
        $this->_updateCustomerSubscription($customerId, $this->isSubscribed());
        return $this;
    }

    /**
     * Saving customer subscription status
     *
     * @param int $customerId
     * @param bool $subscribe indicates whether the customer should be subscribed or unsubscribed
     * @return  $this
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _updateCustomerSubscription($customerId, $subscribe)
    {
        try {
            $customerData = $this->customerRepository->getById($customerId);
        } catch (NoSuchEntityException $e) {
            return $this;
        }

        $this->loadByCustomerId($customerId);
        if (!$subscribe && !$this->getId()) {
            return $this;
        }

        if (!$this->getId()) {
            $this->setDesignConfirmCode($this->randomSequence());
        }

        $sendInformationEmail = false;
        $status = self::STATUS_SUBSCRIBED;
        if ($subscribe) {
            if (AccountManagementInterface::ACCOUNT_CONFIRMATION_REQUIRED
                == $this->customerAccountManagement->getConfirmationStatus($customerId)
            ) {
                $status = self::STATUS_UNCONFIRMED;
            }
        } else {
            $status = self::STATUS_UNSUBSCRIBED;
        }
        /**
         * If subscription status has been changed then send email to the customer
         */
        if ($status != self::STATUS_UNCONFIRMED && $status != $this->getStatus()) {
            $sendInformationEmail = true;
        }

        if ($status != $this->getStatus()) {
            $this->setStatusChanged(true);
        }

        $this->setStatus($status);

        if (!$this->getId()) {
            $storeId = $customerData->getStoreId();
            if ($customerData->getStoreId() == 0) {
                $storeId = $this->_storeManager->getWebsite($customerData->getWebsiteId())->getDefaultStore()->getId();
            }
            $this->setStoreId($storeId)
                ->setCustomerId($customerData->getId())
                ->setEmail($customerData->getEmail());
        } else {
            $this->setStoreId($customerData->getStoreId())
                ->setEmail($customerData->getEmail());
        }

        $this->save();
        $sendSubscription = $sendInformationEmail;
        if ($sendSubscription === null xor $sendSubscription) {
            try {
                if ($this->isStatusChanged() && $status == self::STATUS_UNSUBSCRIBED) {
                    $this->sendUnsubscriptionEmail();
                } elseif ($this->isStatusChanged() && $status == self::STATUS_SUBSCRIBED) {
                    $this->sendConfirmationSuccessEmail();
                }
            } catch (MailException $e) {
                // If we are not able to send a new account email, this should be ignored
                $this->_logger->critical($e);
            }
        }
        return $this;
    }

    /**
     * Confirms design customer
     *
     * @param string $code
     * @return boolean
     */
    public function confirm($code)
    {
        if ($this->getCode() == $code) {
            $this->setStatus(self::STATUS_SUBSCRIBED)
                ->setStatusChanged(true)
                ->save();
            return true;
        }

        return false;
    }

    /**
     * Mark receiving design of queue customer
     *
     * @param  \Designnbuy\Customer\Model\Queue $queue
     * @return boolean
     */
    public function received(\Designnbuy\Customer\Model\Queue $queue)
    {
        $this->getResource()->received($this, $queue);
        return $this;
    }

    /**
     * Sends out confirmation email
     *
     * @return $this
     */
    public function sendConfirmationRequestEmail()
    {
        if ($this->getImportMode()) {
            return $this;
        }

        if (!$this->_scopeConfig->getValue(
            self::XML_PATH_CONFIRM_EMAIL_TEMPLATE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ) || !$this->_scopeConfig->getValue(
            self::XML_PATH_CONFIRM_EMAIL_IDENTITY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        )
        ) {
            return $this;
        }

        $this->inlineTranslation->suspend();

        $this->_transportBuilder->setTemplateIdentifier(
            $this->_scopeConfig->getValue(
                self::XML_PATH_CONFIRM_EMAIL_TEMPLATE,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )
        )->setTemplateOptions(
            [
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $this->_storeManager->getStore()->getId(),
            ]
        )->setTemplateVars(
            ['design' => $this, 'store' => $this->_storeManager->getStore()]
        )->setFrom(
            $this->_scopeConfig->getValue(
                self::XML_PATH_CONFIRM_EMAIL_IDENTITY,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )
        )->addTo(
            $this->getEmail(),
            $this->getName()
        );
        $transport = $this->_transportBuilder->getTransport();
        $transport->sendMessage();

        $this->inlineTranslation->resume();

        return $this;
    }

    /**
     * Sends out confirmation success email
     *
     * @return $this
     */
    public function sendConfirmationSuccessEmail()
    {
        if ($this->getImportMode()) {
            return $this;
        }

        if (!$this->_scopeConfig->getValue(
            self::XML_PATH_SUCCESS_EMAIL_TEMPLATE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ) || !$this->_scopeConfig->getValue(
            self::XML_PATH_SUCCESS_EMAIL_IDENTITY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        )
        ) {
            return $this;
        }

        $this->inlineTranslation->suspend();

        $this->_transportBuilder->setTemplateIdentifier(
            $this->_scopeConfig->getValue(
                self::XML_PATH_SUCCESS_EMAIL_TEMPLATE,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )
        )->setTemplateOptions(
            [
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $this->_storeManager->getStore()->getId(),
            ]
        )->setTemplateVars(
            ['design' => $this]
        )->setFrom(
            $this->_scopeConfig->getValue(
                self::XML_PATH_SUCCESS_EMAIL_IDENTITY,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )
        )->addTo(
            $this->getEmail(),
            $this->getName()
        );
        $transport = $this->_transportBuilder->getTransport();
        $transport->sendMessage();

        $this->inlineTranslation->resume();

        return $this;
    }

    /**
     * Sends out unsubscription email
     *
     * @return $this
     */
    public function sendUnsubscriptionEmail()
    {
        if ($this->getImportMode()) {
            return $this;
        }
        if (!$this->_scopeConfig->getValue(
            self::XML_PATH_UNSUBSCRIBE_EMAIL_TEMPLATE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ) || !$this->_scopeConfig->getValue(
            self::XML_PATH_UNSUBSCRIBE_EMAIL_IDENTITY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        )
        ) {
            return $this;
        }

        $this->inlineTranslation->suspend();

        $this->_transportBuilder->setTemplateIdentifier(
            $this->_scopeConfig->getValue(
                self::XML_PATH_UNSUBSCRIBE_EMAIL_TEMPLATE,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )
        )->setTemplateOptions(
            [
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $this->_storeManager->getStore()->getId(),
            ]
        )->setTemplateVars(
            ['design' => $this]
        )->setFrom(
            $this->_scopeConfig->getValue(
                self::XML_PATH_UNSUBSCRIBE_EMAIL_IDENTITY,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )
        )->addTo(
            $this->getEmail(),
            $this->getName()
        );
        $transport = $this->_transportBuilder->getTransport();
        $transport->sendMessage();

        $this->inlineTranslation->resume();

        return $this;
    }

    /**
     * Retrieve Designs Full Name if it was set
     *
     * @return string|null
     */
    public function getDesignFullName()
    {
        $name = null;
        if ($this->hasFirstname() || $this->hasLastname()) {
            $name = $this->getFirstname() . ' ' . $this->getLastname();
        }
        return $name;
    }
}
