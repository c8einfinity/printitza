<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
// @codingStandardsIgnoreFile
namespace Designnbuy\OrderTicket\Model;

use Magento\Store\Model\Store;
use Magento\Sales\Model\Order\Address;
use Magento\Framework\Api\AttributeValueFactory;
use Designnbuy\OrderTicket\Api\OrderTicketAttributesManagementInterface;

/**
 * ORDERTICKET model
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class OrderTicket extends \Magento\Sales\Model\AbstractModel implements \Designnbuy\OrderTicket\Api\Data\OrderTicketInterface
{
    /**#@+
     * Constants defined for keys of array
     */
    const ENTITY_ID = 'entity_id';

    const ORDER_ID = 'order_id';

    const ORDER_INCREMENT_ID = 'order_increment_id';

    const INCREMENT_ID = 'increment_id';

    const STORE_ID = 'store_id';

    const CUSTOMER_ID = 'customer_id';

    const DATE_REQUESTED = 'date_requested';

    const CUSTOMER_CUSTOM_EMAIL = 'customer_custom_email';

    const ITEMS = 'items';

    const STATUS = 'status';

    const COMMENTS = 'comments';

    const TRACKS = 'tracks';

    /**#@-*/

    /**
     * XML configuration paths
     */
    const XML_PATH_SECTION_ORDERTICKET = 'sales/designnbuy_orderticket/';

    const XML_PATH_ENABLED = 'sales/designnbuy_orderticket/enabled';

    const XML_PATH_USE_STORE_ADDRESS = 'sales/designnbuy_orderticket/use_store_address';

    /**
     * OrderTicket order object
     *
     * @var \Magento\Sales\Model\Order
     */
    protected $_order;

    /**
     * OrderTicket shipping collection
     *
     * @var \Designnbuy\OrderTicket\Model\ResourceModel\Shipping\Collection
     */
    protected $_trackingNumbers;

    /**
     * OrderTicket shipping model
     *
     * @var \Designnbuy\OrderTicket\Model\Shipping
     */
    protected $_shippingLabel;

    /**
     * OrderTicket data
     *
     * @var \Designnbuy\OrderTicket\Helper\Data
     */
    protected $_orderticketData;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    /**
     * Core session model
     *
     * @var \Magento\Framework\Session\Generic
     */
    protected $_session;

    /**
     * Core store manager interface
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Eav configuration model
     *
     * @var \Magento\Eav\Model\Config
     */
    protected $_eavConfig;

    /**
     * OrderTicket item factory
     *
     * @var \Designnbuy\OrderTicket\Model\ItemFactory
     */
    protected $_orderticketItemFactory;


    /**
     * OrderTicket grid factory
     *
     * @var \Designnbuy\OrderTicket\Model\GridFactory
     */
    protected $_orderticketGridFactory;

    /**
     * OrderTicket source status factory
     *
     * @var \Designnbuy\OrderTicket\Model\OrderTicket\Source\StatusFactory
     */
    protected $_statusFactory;


    /**
     * OrderTicket shipping collection factory
     *
     * @var \Designnbuy\OrderTicket\Model\ResourceModel\Shipping\CollectionFactory
     */
    protected $_orderticketShippingFactory;

    /**
     * Sales quote factory
     *
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $_quoteFactory;

    /**
     * Sales quote address rate factory
     *
     * @var \Magento\Quote\Model\Quote\Address\RateFactory
     */
    protected $_quoteRateFactory;

    /**
     * Sales order factory
     *
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_orderFactory;

    /**
     * Sales quote address rate request factory
     *
     * @var \Magento\Quote\Model\Quote\Address\RateRequestFactory
     */
    protected $_rateRequestFactory;

    /**
     * Shipping factory
     *
     * @var \Magento\Shipping\Model\ShippingFactory
     */
    protected $_shippingFactory;

    /**
     * Escaper
     *
     * @var \Magento\Framework\Escaper
     */
    protected $_escaper;

    /**
     * Message manager
     *
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     * @param \Designnbuy\OrderTicket\Helper\Data $orderticketData
     * @param \Magento\Framework\Session\Generic $session
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param ItemFactory $orderticketItemFactory
     * @param Item\Attribute\Source\StatusFactory $attrSourceFactory
     * @param GridFactory $orderticketGridFactory
     * @param OrderTicket\Source\StatusFactory $statusFactory
     * @param \Designnbuy\OrderTicket\Model\ResourceModel\ItemFactory $itemFactory
     * @param \Designnbuy\OrderTicket\Model\ResourceModel\Shipping\CollectionFactory $orderticketShippingFactory
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     * @param \Magento\Quote\Model\Quote\Address\RateFactory $quoteRateFactory
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Quote\Model\Quote\Address\RateRequestFactory $rateRequestFactory
     * @param \Magento\Shipping\Model\ShippingFactory $shippingFactory
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
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
        \Designnbuy\OrderTicket\Helper\Data $orderticketData,
        \Magento\Framework\Session\Generic $session,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Eav\Model\Config $eavConfig,
        \Designnbuy\OrderTicket\Model\GridFactory $orderticketGridFactory,
        \Designnbuy\OrderTicket\Model\OrderTicket\Source\StatusFactory $statusFactory,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Quote\Model\Quote\Address\RateFactory $quoteRateFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Quote\Model\Quote\Address\RateRequestFactory $rateRequestFactory,
        \Magento\Shipping\Model\ShippingFactory $shippingFactory,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_orderticketData = $orderticketData;
        $this->_session = $session;
        $this->_storeManager = $storeManager;
        $this->_eavConfig = $eavConfig;
        $this->_orderticketGridFactory = $orderticketGridFactory;
        $this->_statusFactory = $statusFactory;
        $this->_quoteFactory = $quoteFactory;
        $this->_quoteRateFactory = $quoteRateFactory;
        $this->_orderFactory = $orderFactory;
        $this->_rateRequestFactory = $rateRequestFactory;
        $this->_shippingFactory = $shippingFactory;
        $this->_escaper = $escaper;
        $this->_localeDate = $localeDate;
        $this->messageManager = $messageManager;
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
    public function getIncrementId()
    {
        return $this->getData(self::INCREMENT_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setIncrementId($incrementId)
    {
        return $this->setData(self::INCREMENT_ID, $incrementId);
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
    public function getOrderId()
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderId($orderId)
    {
        return $this->setData(self::ORDER_ID, $orderId);
    }

    /**
     * {@inheritdoc}
     */
    public function getOrderIncrementId()
    {
        return $this->getData(self::ORDER_INCREMENT_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderIncrementId($incrementId)
    {
        return $this->setData(self::ORDER_INCREMENT_ID, $incrementId);
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreId($storeId)
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * {@inheritdoc}
     */
    public function getDateRequested()
    {
        return $this->getData(self::DATE_REQUESTED);
    }

    /**
     * {@inheritdoc}
     */
    public function setDateRequested($dateRequested)
    {
        return $this->setData(self::DATE_REQUESTED, $dateRequested);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerCustomEmail()
    {
        return $this->getData(self::CUSTOMER_CUSTOM_EMAIL);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerCustomEmail($customerCustomEmail)
    {
        return $this->setData(self::CUSTOMER_CUSTOM_EMAIL, $customerCustomEmail);
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
    public function getComments()
    {
        return $this->getData(self::COMMENTS);
    }

    /**
     * {@inheritdoc}
     */
    public function setComments(array $comments = null)
    {
        return $this->setData(self::COMMENTS, $comments);
    }

    /**
     * {@inheritdoc}
     */
    public function getTracks()
    {
        return $this->getData(self::TRACKS);
    }

    /**
     * {@inheritdoc}
     */
    public function setTracks(array $tracks = null)
    {
        return $this->setData(self::TRACKS, $tracks);
    }
    //@codeCoverageIgnoreEnd

    /**
     * Init resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Designnbuy\OrderTicket\Model\ResourceModel\OrderTicket');
        parent::_construct();
    }

    /**
     * Get available statuses for ORDERTICKETs
     *
     * @return array
     */
    public function getAllStatuses()
    {
        /** @var $sourceStatus \Designnbuy\OrderTicket\Model\OrderTicket\Source\Status */
        $sourceStatus = $this->_statusFactory->create();
        return $sourceStatus->getAllOptionsForGrid();
    }

    /**
     * Get ORDERTICKET's status label
     *
     * @return string
     */
    public function getStatusLabel()
    {
        if (is_null(parent::getStatusLabel())) {
            /** @var $sourceStatus \Designnbuy\OrderTicket\Model\OrderTicket\Source\Status */
            $sourceStatus = $this->_statusFactory->create();
            $this->setStatusLabel($sourceStatus->getItemLabel($this->getStatus()));
        }
        return parent::getStatusLabel();
    }

    /**
     * Get orderticket order object
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        if (!$this->_order) {
            $this->_order = $this->_orderFactory->create()->load($this->getOrderId());
        }
        return $this->_order;
    }

    /**
     * Retrieves orderticket close availability
     *
     * @return bool
     */
    public function canClose()
    {
        $status = $this->getStatus();
        if ($status === \Designnbuy\OrderTicket\Model\OrderTicket\Source\Status::STATE_CLOSED
            || $status === \Designnbuy\OrderTicket\Model\OrderTicket\Source\Status::STATE_CLOSED
        ) {
            return false;
        }

        return true;
    }

    /**
     * Close orderticket
     *
     * @return \Designnbuy\OrderTicket\Model\OrderTicket
     */
    public function close()
    {
        if ($this->canClose()) {
            $this->setStatus(\Designnbuy\OrderTicket\Model\OrderTicket\Source\Status::STATE_CLOSED);
        }
        return $this;
    }

    /**
     * Save OrderTicket
     *
     * @param array $data
     * @return bool|$this
     */
    public function saveOrderTicket($data)
    {
        // TODO: move errors adding to controller
        $errors = 0;
        $this->messageManager->getMessages(true);
        if ($this->getCustomerCustomEmail()) {
            $validateEmail = $this->_validateEmail($this->getCustomerCustomEmail());
            if (is_array($validateEmail)) {
                foreach ($validateEmail as $error) {
                    $this->messageManager->addError($error);
                }
                $this->_session->setOrderTicketFormData($data);
                $errors = 1;
            }
        }
        $this->_session->setOrderTicketFormData($data);
        $this->save();
        return $this;
    }

    /**
     * Prepares Item's data
     *
     * @param array $item
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _preparePost($item)
    {
        $errors = false;
        $preparePost = [];
        $qtyKeys = ['qty_authorized', 'qty_returned', 'qty_approved'];

        ksort($item);
        foreach ($item as $key => $value) {
            if ($key == 'order_item_id') {
                $preparePost['order_item_id'] = (int)$value;
            } elseif ($key == 'qty_requested') {
                $preparePost['qty_requested'] = is_numeric($value) ? $value : 0;
            } elseif (in_array($key, $qtyKeys)) {
                if (is_numeric($value)) {
                    $preparePost[$key] = (double)$value;
                } else {
                    $preparePost[$key] = '';
                }
            } elseif ($key == 'resolution') {
                $preparePost['resolution'] = (int)$value;
            } elseif ($key == 'condition') {
                $preparePost['condition'] = (int)$value;
            } elseif ($key == 'reason') {
                $preparePost['reason'] = (int)$value;
            } elseif ($key == 'reason_other' && !empty($value)) {
                $preparePost['reason_other'] = $value;
            } else {
                $preparePost[$key] = $value;
            }
        }

        $order = $this->getOrder();
        $realItem = $order->getItemById($preparePost['order_item_id']);

        $stat = \Designnbuy\OrderTicket\Model\Item\Attribute\Source\Status::STATE_PENDING;
        if (!empty($preparePost['status'])) {
            $stat = $preparePost['status'];
        }

        $preparePost['status'] = $stat;

        $preparePost['product_name'] = $realItem->getName();
        $preparePost['product_sku'] = $realItem->getSku();
        $preparePost['product_admin_name'] = $this->_orderticketData->getAdminProductName($realItem);
        $preparePost['product_admin_sku'] = $this->_orderticketData->getAdminProductSku($realItem);
        $preparePost['product_options'] = serialize($realItem->getProductOptions());
        $preparePost['is_qty_decimal'] = $realItem->getIsQtyDecimal();

        if ($preparePost['is_qty_decimal']) {
            $preparePost['qty_requested'] = (double)$preparePost['qty_requested'];
        } else {
            $preparePost['qty_requested'] = (int)$preparePost['qty_requested'];

            foreach ($qtyKeys as $key) {
                if (!empty($preparePost[$key])) {
                    $preparePost[$key] = (int)$preparePost[$key];
                }
            }
        }

        if (isset($preparePost['qty_requested']) && $preparePost['qty_requested'] <= 0) {
            $errors = true;
        }

        foreach ($qtyKeys as $key) {
            if (isset($preparePost[$key]) && !is_string($preparePost[$key]) && $preparePost[$key] <= 0) {
                $errors = true;
            }
        }

        if ($errors) {
            $this->messageManager->addError(
                __('There is an error in quantities for item %1.', $preparePost['product_name'])
            );
        }

        return $preparePost;
    }

    /**
     * Checks Items Quantity in Return
     *
     * @param  Item $itemModels
     * @param  int $orderId
     * @return array|bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _checkPost($itemModels, $orderId)
    {
        $errors = [];
        $errorKeys = [];
        if (!$this->getIsUpdate()) {
            $availableItems = $this->_orderticketData->getOrderItems($orderId);
        } else {
            /** @var $itemResource \Designnbuy\OrderTicket\Model\ResourceModel\Item */
            $itemResource = $this->_itemFactory->create();
            $availableItems = $itemResource->getOrderItemsCollection($orderId);
        }

        $itemsArray = [];
        foreach ($itemModels as $item) {
            if (!isset($itemsArray[$item->getOrderItemId()])) {
                $itemsArray[$item->getOrderItemId()] = $item->getQtyRequested();
            } else {
                $itemsArray[$item->getOrderItemId()] += $item->getQtyRequested();
            }

            if ($this->getIsUpdate()) {
                $validation = [];
                foreach (['qty_requested', 'qty_authorized', 'qty_returned', 'qty_approved'] as $tempQty) {
                    if (is_null($item->getData($tempQty))) {
                        if (!is_null($item->getOrigData($tempQty))) {
                            $validation[$tempQty] = (double)$item->getOrigData($tempQty);
                        }
                    } else {
                        $validation[$tempQty] = (double)$item->getData($tempQty);
                    }
                }
                $validation['dummy'] = -1;
                $previousValue = null;
                $escapedProductName = $this->_escaper->escapeHtml($item->getProductName());
                foreach ($validation as $key => $value) {
                    if (isset($previousValue) && $value > $previousValue) {
                        $errors[] = __('There is an error in quantities for item %1.', $escapedProductName);
                        $errorKeys[$item->getId()] = $key;
                        $errorKeys['tabs'] = 'items_section';
                        break;
                    }
                    $previousValue = $value;
                }

                //if we change item status i.e. to authorized, then qty_authorized must be non-empty and so on.
                $qtyToStatus = [
                    'qty_authorized' => [
                        'name' => __('Authorized Qty'),
                        'status' => \Designnbuy\OrderTicket\Model\OrderTicket\Source\Status::STATE_AUTHORIZED,
                    ],
                    'qty_returned' => [
                        'name' => __('Returned Qty'),
                        'status' => \Designnbuy\OrderTicket\Model\OrderTicket\Source\Status::STATE_RECEIVED,
                    ],
                    'qty_approved' => [
                        'name' => __('Approved Qty'),
                        'status' => \Designnbuy\OrderTicket\Model\OrderTicket\Source\Status::STATE_APPROVED,
                    ],
                ];
                foreach ($qtyToStatus as $qtyKey => $qtyValue) {
                    if ($item->getStatus() === $qtyValue['status']
                        && $item->getOrigData(
                            'status'
                        ) !== $qtyValue['status']
                        && !$item->getData(
                            $qtyKey
                        )
                    ) {
                        $errors[] = __('%1 for item %2 cannot be empty.', $qtyValue['name'], $escapedProductName);
                        $errorKeys[$item->getId()] = $qtyKey;
                        $errorKeys['tabs'] = 'items_section';
                    }
                }
            }
        }
        ksort($itemsArray);

        $availableItemsArray = [];
        foreach ($availableItems as $item) {
            $availableItemsArray[$item->getId()] = [
                'name' => $item->getName(),
                'qty' => $item->getAvailableQty(),
            ];
        }

        foreach ($itemsArray as $key => $qty) {
            $escapedProductName = $this->_escaper->escapeHtml($availableItemsArray[$key]['name']);
            if (!array_key_exists($key, $availableItemsArray)) {
                $errors[] = __('You cannot return %1.', $escapedProductName);
            }
            if (isset($availableItemsArray[$key]) && $availableItemsArray[$key]['qty'] < $qty) {
                $errors[] = __('A quantity of %1 is greater than you can return.', $escapedProductName);
                $errorKeys[$key] = 'qty_requested';
                $errorKeys['tabs'] = 'items_section';
            }
        }

        if (!empty($errors)) {
            return [$errors, $errorKeys];
        }
        return true;
    }

    /**
     * Creates orderticket items collection by passed data
     *
     * @param array $data
     * @return Item[]
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _createItemsCollection($data)
    {
        if (!is_array($data)) {
            $data = (array)$data;
        }
        $order = $this->getOrder();
        $itemModels = [];
        $errors = [];
        $errorKeys = [];

        foreach ($data['items'] as $key => $item) {
            if (isset($item['items'])) {
                $itemModel = $firstModel = false;
                $files = $f = [];
                foreach ($item['items'] as $id => $qty) {
                    if ($itemModel) {
                        $firstModel = $itemModel;
                    }
                    /** @var $itemModel Item */
                    $itemModel = $this->_orderticketItemFactory->create();
                    $subItem = $item;
                    unset($subItem['items']);
                    $subItem['order_item_id'] = $id;
                    $subItem['qty_requested'] = $qty;

                    $itemPost = $this->_preparePost($subItem);

                    $f = $itemModel->setData($itemPost)->prepareAttributes($itemPost, $key);

                    /* Copy image(s) to another bundle items */
                    if (!empty($f)) {
                        $files = $f;
                    }
                    if (!empty($files) && $firstModel) {
                        foreach ($files as $code) {
                            $itemModel->setData($code, $firstModel->getData($code));
                        }
                    }
                    $errors = array_merge($itemModel->getErrors(), $errors);

                    $itemModels[] = $itemModel;
                }
            } else {
                /** @var $itemModel Item */
                $itemModel = $this->_orderticketItemFactory->create();
                if (isset($item['entity_id']) && $item['entity_id']) {
                    $itemModel->load($item['entity_id']);
                    if ($itemModel->getEntityId()) {
                        if (empty($item['reason'])) {
                            $item['reason'] = $itemModel->getReason();
                        }

                        if (empty($item['reason_other'])) {
                            $item['reason_other'] =
                                $itemModel->getReasonOther() === null ? '' : $itemModel->getReasonOther();
                        }

                        if (empty($item['condition'])) {
                            $item['condition'] = $itemModel->getCondition();
                        }

                        if (empty($item['qty_requested'])) {
                            $item['qty_requested'] = $itemModel->getQtyRequested();
                        }
                    }
                }

                $itemPost = $this->_preparePost($item);

                $itemModel->setData($itemPost)->prepareAttributes($itemPost, $key);
                $errors = array_merge($itemModel->getErrors(), $errors);
                if ($errors) {
                    $errorKeys['tabs'] = 'items_section';
                }

                $itemModels[] = $itemModel;

                if ($itemModel->getStatus() === \Designnbuy\OrderTicket\Model\Item\Attribute\Source\Status::STATE_AUTHORIZED
                    && $itemModel->getOrigData(
                        'status'
                    ) !== $itemModel->getStatus()
                ) {
                    $this->setIsSendAuthEmail(1);
                }
            }
        }

        $result = $this->_checkPost($itemModels, $order->getId());

        if ($result !== true) {
            list($result, $errorKey) = $result;
            $errors = array_merge($result, $errors);
            $errorKeys = array_merge($errorKey, $errorKeys);
        }

        $eMessages = $this->messageManager->getMessages()->getErrors();
        if (!empty($errors) || !empty($eMessages)) {
            $this->_session->setOrderTicketFormData($data);
            if (!empty($errorKeys)) {
                $this->_session->setOrderTicketErrorKeys($errorKeys);
            }
            if (!empty($errors)) {
                foreach ($errors as $message) {
                    $this->messageManager->addError($message);
                }
            }
            return false;
        }
        $this->setItems($itemModels);

        return $this->getItems();
    }

    /**
     * Validate email
     *
     * @param string $value
     * @return string
     */
    protected function _validateEmail($value)
    {
        $label = $this->_orderticketData->getContactEmailLabel();

        $validator = new \Zend_Validate_EmailAddress();
        $validator->setMessage(__('You entered an invalid type: "%1".', $label), \Zend_Validate_EmailAddress::INVALID);
        $validator->setMessage(
            __('You entered an invalid email address: "%1".', $label),
            \Zend_Validate_EmailAddress::INVALID_FORMAT
        );
        $validator->setMessage(
            __('You entered an invalid hostname: "%1"', $label),
            \Zend_Validate_EmailAddress::INVALID_HOSTNAME
        );
        $validator->setMessage(
            __('You entered an invalid hostname: "%1"', $label),
            \Zend_Validate_EmailAddress::INVALID_MX_RECORD
        );
        $validator->setMessage(
            __('You entered an invalid hostname: "%1"', $label),
            \Zend_Validate_EmailAddress::INVALID_MX_RECORD
        );
        $validator->setMessage(
            __('You entered an invalid email address: "%1".', $label),
            \Zend_Validate_EmailAddress::DOT_ATOM
        );
        $validator->setMessage(
            __('You entered an invalid email address: "%1".', $label),
            \Zend_Validate_EmailAddress::QUOTED_STRING
        );
        $validator->setMessage(
            __('You entered an invalid email address: "%1".', $label),
            \Zend_Validate_EmailAddress::INVALID_LOCAL_PART
        );
        $validator->setMessage(
            __('"%1" is longer than allowed.', $label),
            \Zend_Validate_EmailAddress::LENGTH_EXCEEDED
        );
        if (!$validator->isValid($value)) {
            return array_unique($validator->getMessages());
        }

        return true;
    }

    /**
     * Get foorderticketted ORDERTICKET created date in store timezone
     *
     * @param   string $foordertickett date foordertickett type (short|medium|long|full)
     * @return  string
     */
    public function getCreatedAtFormated($format)
    {
        $storeTimezone = $this->_localeDate->getConfigTimezone(
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->_storeManager->getStore($this->getStoreId())->getCode()
        );
        $requestedDate = new \DateTime($this->getDateRequested());
        $scopeDate = $this->_localeDate->formatDateTime(
            $requestedDate,
            $format,
            $format,
            null,
            $storeTimezone
        );
        return $scopeDate;
    }


    /**
     * Defines whether ORDERTICKET status and ORDERTICKET Items statuses allow to create shipping label
     *
     * @return bool
     */
    public function isAvailableForPrintLabel()
    {
        return (bool)($this->_isOrderTicketAvailableForPrintLabel() && $this->_isItemsAvailableForPrintLabel());
    }

    /**
     * Defines whether ORDERTICKET status allow to create shipping label
     *
     * @return bool
     */
    protected function _isOrderTicketAvailableForPrintLabel()
    {
        return $this->getStatus() !== \Designnbuy\OrderTicket\Model\OrderTicket\Source\Status::STATE_CLOSED
        && $this->getStatus() !== \Designnbuy\OrderTicket\Model\OrderTicket\Source\Status::STATE_CLOSED
        && $this->getStatus() !== \Designnbuy\OrderTicket\Model\OrderTicket\Source\Status::STATE_PENDING;
    }



    /**
     * Get button disabled status
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getButtonDisabledStatus()
    {
        /** @var $sourceStatus \Designnbuy\OrderTicket\Model\OrderTicket\Source\Status */
        $sourceStatus = $this->_statusFactory->create();
        return $sourceStatus->getButtonDisabledStatus($this->getStatus()) && $this->_isItemsNotInPendingStatus();
    }


}
