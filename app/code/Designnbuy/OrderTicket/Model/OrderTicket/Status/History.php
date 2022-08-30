<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Model\OrderTicket\Status;

use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Designnbuy\OrderTicket\Model\OrderTicket;
use Designnbuy\OrderTicket\Model\OrderTicket\Source\Status;
use Designnbuy\OrderTicket\Api\OrderTicketRepositoryInterface;
use Designnbuy\OrderTicket\Api\OrderTicketAttributesManagementInterface;
use Designnbuy\OrderTicket\Api\Data\CommentInterface;
use Magento\Sales\Model\Order\Address\Renderer as AddressRenderer;

/**
 * ORDERTICKET model
 * @method \Designnbuy\OrderTicket\Model\OrderTicket\Status\History setStoreId(int $storeId)
 * @method \Designnbuy\OrderTicket\Model\OrderTicket\Status\History setEmailSent(bool $value)
 * @method bool getEmailSent()
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class History extends \Magento\Sales\Model\AbstractModel implements CommentInterface
{
    /**#@+
     * Data object properties
     */
    const ENTITY_ID = 'entity_id';
    const ORDERTICKET_ENTITY_ID = 'orderticket_entity_id';
    const IS_CUSTOMER_NOTIFIED = 'is_customer_notified';
    const IS_VISIBLE_ON_FRONT = 'is_visible_on_front';
    const COMMENT = 'comment';
    const FILE = 'file';
    const STATUS = 'status';
    const CREATED_AT = 'created_at';
    const IS_ADMIN = 'is_admin';

    /** @deprecated  use IS_ADMIN instead*/
    const ADMIN = 'admin';

    /** @deprecated  use IS_CUSTOMER_NOTIFIED instead*/
    const CUSTOMER_NOTIFIED = 'customer_notified';

    /** @deprecated use IS_VISIBLE_ON_FRONT instead*/
    const VISIBLE_ON_FRONT = 'visible_on_front';

    /**
     * Core store manager interface
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * OrderTicket factory
     *
     * @var \Designnbuy\OrderTicket\Model\OrderTicketFactory
     */
    protected $_orderticketFactory;

    /**
     * OrderTicket configuration
     *
     * @var \Designnbuy\OrderTicket\Model\Config
     */
    protected $_orderticketConfig;

    /**
     * Mail transport builder
     *
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * Core date model 2.0
     *
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTimeDateTime;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var \Designnbuy\OrderTicket\Helper\Data
     */
    protected $orderticketHelper;

    /**
     * @var TimezoneInterface
     */
    protected $localeDate;

    /**
     * @var OrderTicketRepositoryInterface
     */
    protected $orderticketRepository;

    /**
     * @var AddressRenderer
     */
    protected $addressRenderer;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Designnbuy\OrderTicket\Model\OrderTicketFactory $orderticketFactory
     * @param \Designnbuy\OrderTicket\Model\Config $orderticketConfig
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTimeDateTime
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Designnbuy\OrderTicket\Helper\Data $orderticketHelper
     * @param TimezoneInterface $localeDate
     * @param OrderTicketRepositoryInterface $orderticketRepositoryInterface
     * @param AddressRenderer $addressRenderer
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Designnbuy\OrderTicket\Model\OrderTicketFactory $orderticketFactory,
        \Designnbuy\OrderTicket\Model\Config $orderticketConfig,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTimeDateTime,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Designnbuy\OrderTicket\Helper\Data $orderticketHelper,
        TimezoneInterface $localeDate,
        OrderTicketRepositoryInterface $orderticketRepositoryInterface,
        AddressRenderer $addressRenderer,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_storeManager = $storeManager;
        $this->_orderticketFactory = $orderticketFactory;
        $this->_orderticketConfig = $orderticketConfig;
        $this->_transportBuilder = $transportBuilder;
        $this->dateTimeDateTime = $dateTimeDateTime;
        $this->inlineTranslation = $inlineTranslation;
        $this->orderticketHelper = $orderticketHelper;
        $this->localeDate = $localeDate;
        $this->orderticketRepository = $orderticketRepositoryInterface;
        $this->addressRenderer = $addressRenderer;
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * @codeCoverageIgnoreStart
     * {@inheritdoc}
     */
    public function getComment()
    {
        return $this->getData(self::COMMENT);
    }

    /**
     * {@inheritdoc}
     */
    public function setComment($comment)
    {
        return $this->setData(self::COMMENT, $comment);
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderTicketEntityId()
    {
        return $this->getData(self::ORDERTICKET_ENTITY_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderTicketEntityId($orderticketId)
    {
        return $this->setData(self::ORDERTICKET_ENTITY_ID, $orderticketId);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityId()
    {
        return $this->getData(self::ENTITY_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setEntityId($entityId)
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * {@inheritdoc}
     */
    public function isCustomerNotified()
    {
        return $this->getData(self::IS_CUSTOMER_NOTIFIED);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsCustomerNotified($isCustomerNotified)
    {
        return $this->setData(self::IS_CUSTOMER_NOTIFIED, $isCustomerNotified);
    }

    /**
     * {@inheritdoc}
     */
    public function isVisibleOnFront()
    {
        return $this->getData(self::IS_VISIBLE_ON_FRONT);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsVisibleOnFront($isVisibleOnFront)
    {
        return $this->setData(self::IS_VISIBLE_ON_FRONT, $isVisibleOnFront);
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * {@inheritdoc}
     */
    public function isAdmin()
    {
        return $this->getData(self::IS_ADMIN);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsAdmin($isAdmin)
    {
        return $this->setData(self::IS_ADMIN, $isAdmin);
    }
    //@codeCoverageIgnoreEnd

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Designnbuy\OrderTicket\Model\ResourceModel\OrderTicket\Status\History');
    }

    /**
     * Get store object
     *
     * @return \Magento\Store\Model\Store
     */
    public function getStore()
    {
        if ($this->getOrder()) {
            return $this->getOrder()->getStore();
        }
        return $this->_storeManager->getStore();
    }

    /**
     * Get ORDERTICKET object
     *
     * @return \Designnbuy\OrderTicket\Model\OrderTicket
     */
    public function getOrderTicket()
    {
        return $this->orderticketRepository->get($this->getOrderTicketEntityId());
    }

    /**
     * Sending email with comment data
     *
     * @return $this
     */
    public function sendCommentEmail()
    {
        $order = $this->getOrderTicket()->getOrder();
        if ($order->getCustomerIsGuest()) {
            $customerName = $order->getBillingAddress()->getName();
        } else {
            $customerName = $order->getCustomerName();
        }
        $sendTo = [['email' => $order->getCustomerEmail(), 'name' => $customerName]];

        return $this->_sendCommentEmail($this->_orderticketConfig->getRootCommentEmail(), $sendTo, true);
    }

    /**
     * Sending email to admin with customer's comment data
     *
     * @return $this
     */
    public function sendCustomerCommentEmail()
    {
        $orderticketModel = $this->orderticketRepository->get($this->getOrderTicketEntityId());
        $sendTo = [
            [
                'email' => $this->_orderticketConfig->getCustomerEmailRecipient($orderticketModel->getStoreId()),
                'name' => null,
            ],
        ];
        return $this->_sendCommentEmail(
            $this->_orderticketConfig->getRootCustomerCommentEmail(),
            $sendTo,
            false,
            \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE
        );
    }

    /**
     * Sending email to admin with customer's comment data
     *
     * @param string $rootConfig Current config root
     * @param array $sendTo mail recipient array
     * @param bool $isGuestAvailable
     * @param string $templateArea
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _sendCommentEmail(
        $rootConfig,
        $sendTo,
        $isGuestAvailable = true,
        $templateArea = \Magento\Framework\App\Area::AREA_FRONTEND
    ) {
        $orderticket = $this->getOrderTicket();

        $this->_orderticketConfig->init($rootConfig, $orderticket->getStoreId());
        if (!$this->_orderticketConfig->isEnabled()) {
            return $this;
        }

        $this->inlineTranslation->suspend();

        $copyTo = $this->_orderticketConfig->getCopyTo();
        $copyMethod = $this->_orderticketConfig->getCopyMethod();

        if ($isGuestAvailable && $orderticket->getOrder()->getCustomerIsGuest()) {
            $template = $this->_orderticketConfig->getGuestTemplate();
        } else {
            $template = $this->_orderticketConfig->getTemplate();
        }

        if ($copyTo && $copyMethod == 'copy') {
            foreach ($copyTo as $email) {
                $sendTo[] = ['email' => $email, 'name' => null];
            }
        }

        $bcc = [];
        if ($copyTo && $copyMethod == 'bcc') {
            $bcc = $copyTo;
        }

        if ($templateArea == \Magento\Framework\App\Area::AREA_FRONTEND) {
            $storeId = $orderticket->getStoreId();
        } else {
            $storeId = \Magento\Store\Model\Store::DEFAULT_STORE_ID;
        }

        foreach ($sendTo as $recipient) {
            $transport = $this->_transportBuilder->setTemplateIdentifier($template)
                ->setTemplateOptions(
                    ['area' => $templateArea, 'store' => $storeId]
                )
                ->setTemplateVars(['orderticket' => $orderticket, 'order' => $orderticket->getOrder(), 'comment' => $this->getComment()])
                ->setFrom($this->_orderticketConfig->getIdentity())
                ->addTo($recipient['email'], $recipient['name'])
                ->addBcc($bcc)
                ->getTransport();

            $transport->sendMessage();
        }
        $this->setEmailSent(true);

        $this->inlineTranslation->resume();

        return $this;
    }

    /**
     * Save system comment
     *
     * @return null
     */
    public function saveSystemComment()
    {
        $status = $this->getOrderTicket()->getStatus();
        $comment = self::getSystemCommentByStatus($status);
        if ($status === $this->getOrderTicket()->getOrigData('status') && $comment) {
            return;
        }
        $this->saveComment($comment, true, true);
    }

    /**
     * Get system comment by state
     * Returns null if state is not known
     *
     * @param string $status
     * @return string|null
     */
    public static function getSystemCommentByStatus($status)
    {
        $comments = [
            Status::STATE_PENDING => __('We received your Order ticket.'),
            Status::STATE_OPEN => __('We received your Return request.'),
            Status::STATE_CLOSED => __('We closed your Return request.'),
        ];
        return isset($comments[$status]) ? $comments[$status] : null;
    }

    /**
     * @param string $comment
     * @param bool $visibleOnFrontend
     * @param bool $isAdmin
     * @return void
     */
    public function saveComment($comment, $visibleOnFrontend, $isAdmin = false)
    {
        /** @var \Designnbuy\OrderTicket\Api\Data\OrderTicketInterface $orderticket */
        $orderticket = $this->getOrderTicket();
        $this->setOrderTicketEntityId($orderticket->getEntityId());
        $this->setComment($comment);
        $this->setFile($this->getFile());
        $this->setIsVisibleOnFront($visibleOnFrontend);
        $this->setStatus($orderticket->getStatus());
        $this->setCreatedAt($this->dateTimeDateTime->gmtDate());
        $this->setIsCustomerNotified($this->isCustomerNotified());
        $this->setIsAdmin($isAdmin);
        $this->save();
    }

    /**
     * Sending email with ORDERTICKET data
     *
     * @return $this
     */
    public function sendNewOrderTicketEmail()
    {
        return $this->_sendOrderTicketEmailWithItems($this->getOrderTicket(), $this->_orderticketConfig->getRootOrderTicketEmail());
    }

    /**
     * Sending authorizing email with ORDERTICKET data
     *
     * @return $this
     */
    public function sendAuthorizeEmail()
    {
        $orderticket = $this->getOrderTicket();
        if (!$orderticket->getIsSendAuthEmail()) {
            return $this;
        }
        return $this->_sendOrderTicketEmailWithItems($orderticket, $this->_orderticketConfig->getRootAuthEmail());
    }

    /**
     * Sending authorizing email with ORDERTICKET data
     *
     * @param OrderTicket $orderticket
     * @param string $rootConfig
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _sendOrderTicketEmailWithItems(OrderTicket $orderticket, $rootConfig)
    {
        $storeId = $orderticket->getStoreId();
        $order = $orderticket->getOrder();

        $this->_orderticketConfig->init($rootConfig, $storeId);
        if (!$this->_orderticketConfig->isEnabled()) {
            return $this;
        }

        $this->inlineTranslation->suspend();

        $copyTo = $this->_orderticketConfig->getCopyTo();
        $copyMethod = $this->_orderticketConfig->getCopyMethod();

        if ($order->getCustomerIsGuest()) {
            $template = $this->_orderticketConfig->getGuestTemplate();
            $customerName = $order->getBillingAddress()->getName();
        } else {
            $template = $this->_orderticketConfig->getTemplate();
            $customerName = $orderticket->getCustomerName();
        }

        $sendTo = [['email' => $order->getCustomerEmail(), 'name' => $customerName]];
        if ($orderticket->getCustomerCustomEmail()) {
            $sendTo[] = ['email' => $orderticket->getCustomerCustomEmail(), 'name' => $customerName];
        }
        if ($copyTo && $copyMethod == 'copy') {
            foreach ($copyTo as $email) {
                $sendTo[] = ['email' => $email, 'name' => null];
            }
        }

        $returnAddress = $this->orderticketHelper->getReturnAddress('html', [], $storeId);

        $bcc = [];
        if ($copyTo && $copyMethod == 'bcc') {
            $bcc = $copyTo;
        }

        foreach ($sendTo as $recipient) {
            $transport = $this->_transportBuilder->setTemplateIdentifier($template)
                ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $storeId])
                ->setTemplateVars(
                    [
                        'orderticket' => $orderticket,
                        'order' => $order,
                        'store' => $this->getStore(),
                        'return_address' => $returnAddress,
                        'item_collection' => $orderticket->getItemsForDisplay(),
                        'formattedShippingAddress' => $this->addressRenderer->format(
                            $order->getShippingAddress(),
                            'html'
                        ),
                        'formattedBillingAddress' => $this->addressRenderer->format(
                            $order->getBillingAddress(),
                            'html'
                        ),
                    ]
                )
                ->setFrom($this->_orderticketConfig->getIdentity())
                ->addTo($recipient['email'], $recipient['name'])
                ->addBcc($bcc)
                ->getTransport();

            $transport->sendMessage();
        }

        $this->setEmailSent(true);

        $this->inlineTranslation->resume();

        return $this;
    }

    /**
     * Get object created at date affected current active store timezone
     *
     * @return \DateTime
     */
    public function getCreatedAtDate()
    {
        return $this->localeDate->date(new \DateTime($this->getCreatedAt()));
    }

    /**
     * @codeCoverageIgnoreStart
     * {@inheritdoc}
     */
    public function getFile()
    {
        return $this->getData(self::FILE);
    }

    /**
     * {@inheritdoc}
     */
    public function setFile($file)
    {
        return $this->setData(self::FILE, $file);
    }
}
