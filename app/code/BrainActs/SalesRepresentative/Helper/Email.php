<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
use Magento\User\Model\User as AdminUser;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Class Email
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class Email extends AbstractHelper
{
    const XML_PATH_EMAIL_ALLOWED_FIELD = 'brainacts_salesrep/admin/email_order_assigned';

    const XML_PATH_EMAIL_TEMPLATE_FIELD = 'brainacts_salesrep/admin/template_notification';

    const XML_PATH_EMAIL_CONFIRMATION_EMAIL = 'brainacts_salesrep/general/admin_email';

    /**
     * Sender email config path
     */
    const XML_PATH_EMAIL_SENDER = 'contact/email/sender_email_identity';

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    private $inlineTranslation;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var string
     */
    private $templateId;

    /**
     * @var \BrainActs\SalesRepresentative\Api\MemberRepositoryInterface
     */
    private $memberRepositoryInterface;

    /**
     * @var \Magento\User\Model\User
     */
    private $userModel;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepositoryInterface;

    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    private $backendUrl;

    /**
     * @var ContactConfig
     */
    private $contactsConfig;

    /**
     * Email constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \BrainActs\SalesRepresentative\Api\MemberRepositoryInterface $memberRepositoryInterface
     * @param AdminUser $userModel
     * @param OrderRepositoryInterface $orderRepositoryInterface
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \BrainActs\SalesRepresentative\Api\MemberRepositoryInterface $memberRepositoryInterface,
        AdminUser $userModel,
        OrderRepositoryInterface $orderRepositoryInterface,
        \Magento\Backend\Model\UrlInterface $backendUrl
    ) {
        parent::__construct($context);
        $this->storeManager = $storeManager;
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
        $this->memberRepositoryInterface = $memberRepositoryInterface;
        $this->userModel = $userModel;
        $this->orderRepositoryInterface = $orderRepositoryInterface;
        $this->backendUrl = $backendUrl;
    }

    /**
     * Return store configuration value of your template field that which id you set for template
     *
     * @param string $path
     * @param int $storeId
     * @return mixed
     */
    private function getConfigValue($path, $storeId)
    {
        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Return store
     * @return \Magento\Store\Api\Data\StoreInterface
     */
    public function getStore()
    {
        return $this->storeManager->getStore();
    }

    /**
     * Return template id according to store
     *
     * @return mixed
     */
    public function getTemplateId($xmlPath)
    {
        return $this->getConfigValue($xmlPath, $this->getStore()->getStoreId());
    }

    /**
     * @param $emailTemplateVariables
     * @param $receiverInfo
     * @return $this
     */
    public function generateTemplate($emailTemplateVariables, $receiverInfo)
    {
        $this->transportBuilder->setTemplateIdentifier($this->templateId)
            ->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_ADMINHTML,
                    'store' => $this->storeManager->getStore()->getId(),
                ]
            )
            ->setTemplateVars($emailTemplateVariables)
            ->setFrom($this->emailSender())
            ->addTo($receiverInfo['email'], $receiverInfo['name']);

        return $this;
    }

    public function emailSender()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_SENDER,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param $memberId
     * @param $orderId
     * @return Email
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     */
    public function notifySalesRepresentative($memberId, $orderId)
    {

        if (!$this->isAllowedNotification()) {
            return $this;
        }

        $member = $this->memberRepositoryInterface->getById($memberId);

        $userId = $member->getUserId();
        if (empty($userId)) {
            return $this;
        }

        $admin = $this->userModel->load($userId);
        $order = $this->orderRepositoryInterface->get($orderId);

        /* Receiver Detail */
        $receiverInfo = [
            'name' => $admin->getName(),
            'email' => $admin->getEmail()
        ];

        /* Assign values for your template variables  */
        $emailTemplateVariables = [];
        $emailTemplateVariables['memberName'] = $member->getFirstname() . ' ' . $member->getLastname();
        $emailTemplateVariables['orderNumber'] = $order->getIncrementId();
        $emailTemplateVariables['orderUrl'] = $this->backendUrl->getUrl('sales/order/view', [
            'order_id' => $order->getId()
        ]);

        $this->templateId = $this->getTemplateId(self::XML_PATH_EMAIL_TEMPLATE_FIELD);
        $this->inlineTranslation->suspend();
        $this->generateTemplate($emailTemplateVariables, $receiverInfo);
        $transport = $this->transportBuilder->getTransport();
        $transport->sendMessage();
        $this->inlineTranslation->resume();

        return $this;
    }

    private function isAllowedNotification()
    {
        $isAllowed = (bool)$this->getConfigValue(self::XML_PATH_EMAIL_ALLOWED_FIELD, $this->getStore()->getStoreId());
        return $isAllowed;
    }

    /**
     * Return email for confirmation
     * @return mixed
     */
    private function getConfirmationEmailRecipient()
    {
        $email = $this->getConfigValue(self::XML_PATH_EMAIL_CONFIRMATION_EMAIL, $this->getStore()->getStoreId());
        return $email;
    }

    /**
     * Request confirmation about change SR for customer
     * @param array $vars
     * @return $this
     * @throws \Magento\Framework\Exception\MailException
     */
    public function sendConfirmChangeMember($vars)
    {

        /* Receiver Detail */
        $receiverInfo = [
            'name' => '',
            'email' => $this->getConfirmationEmailRecipient()
        ];

        $this->templateId = 'brainacts_salesrep_admin_template_confirmation';
        $this->inlineTranslation->suspend();
        $this->generateTemplate($vars, $receiverInfo);
        $transport = $this->transportBuilder->getTransport();
        $transport->sendMessage();
        $this->inlineTranslation->resume();

        return $this;
    }
}
