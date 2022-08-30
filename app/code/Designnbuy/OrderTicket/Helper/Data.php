<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * ORDERTICKET Helper
 */
namespace Designnbuy\OrderTicket\Helper;

use Designnbuy\OrderTicket\Model\OrderTicket;
use Designnbuy\OrderTicket\Model\Shipping;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const FILE_PATH = 'designnbuy'. DIRECTORY_SEPARATOR .'ordertickets'. DIRECTORY_SEPARATOR;
    /**
     * Variable to contain country model
     *
     * @var \Magento\Directory\Model\Country
     */
    protected $_countryModel = null;

    /**
     * Variable to contain order items collection for ORDERTICKET creating
     *
     * @var \Magento\Sales\Model\ResourceModel\Order\Item\Collection
     */
    protected $_orderItems = null;

    /**
     * Allowed hash keys for shipment tracking
     *
     * @var string[]
     */
    protected $_allowedHashKeys = ['orderticket_id', 'track_id'];

    /**
     * Country factory
     *
     * @var \Magento\Directory\Model\CountryFactory
     */
    protected $_countryFactory;

    /**
     * Region factory
     *
     * @var \Magento\Directory\Model\RegionFactory
     */
    protected $_regionFactory;

    /**
     * Core store manager interface
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    /**
     * Customer session model
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * Backend authorization session model
     *
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_authSession;

    /**
     * Sales quote address factory
     *
     * @var \Magento\Quote\Model\Quote\AddressFactory
     */
    protected $_addressFactory;

    /**
     * Shipping carrier factory
     *
     * @var \Magento\Shipping\Model\CarrierFactory
     */
    protected $_carrierFactory;

    /**
     * Filter manager
     *
     * @var \Magento\Framework\Filter\FilterManager
     */
    protected $_filterManager;

    /**
     * Date time foordertickettter
     *
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * @var \Magento\Sales\Model\Order\Admin\Item
     */
    protected $adminOrderItem;

    /**
     * Shipping carrier helper
     *
     * @var \Magento\Shipping\Helper\Carrier
     */
    protected $carrierHelper;

    /**
     * Media Directory object (writable).
     *
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $_mediaDirectory;


    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Quote\Model\Quote\AddressFactory $addressFactory
     * @param \Magento\Shipping\Model\CarrierFactory $carrierFactory
     * @param \Magento\Framework\Filter\FilterManager $filterManager
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param \Magento\Sales\Model\Order\Admin\Item $adminOrderItem
     * @param \Magento\Shipping\Helper\Carrier $carrierHelper
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Quote\Model\Quote\AddressFactory $addressFactory,
        \Magento\Shipping\Model\CarrierFactory $carrierFactory,
        \Magento\Framework\Filter\FilterManager $filterManager,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Sales\Model\Order\Admin\Item $adminOrderItem,
        \Magento\Shipping\Helper\Carrier $carrierHelper,
        \Magento\Framework\Filesystem $filesystem
    ) {
        $this->_countryFactory = $countryFactory;
        $this->_regionFactory = $regionFactory;
        $this->_storeManager = $storeManager;
        $this->_localeDate = $localeDate;
        $this->_customerSession = $customerSession;
        $this->_authSession = $authSession;
        $this->_addressFactory = $addressFactory;
        $this->_carrierFactory = $carrierFactory;
        $this->_filterManager = $filterManager;
        $this->dateTime = $dateTime;
        $this->adminOrderItem = $adminOrderItem;
        $this->carrierHelper = $carrierHelper;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        parent::__construct($context);
    }

    /**
     * Checks whether ORDERTICKET module is enabled for frontend in system config
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            OrderTicket::XML_PATH_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Checks for ability to create ORDERTICKET
     *
     * @param  int|\Magento\Sales\Model\Order $order
     * @param  bool $forceCreate - set yes when you don't need to check config setting (for admin side)
     * @return bool
     */
    public function canCreateOrderTicket($order, $forceCreate = false)
    {

        if ($forceCreate || $this->isEnabled()) {
            return true;
        }

        return false;
    }

    /**
     * Get url for orderticket create
     *
     * @param  \Magento\Sales\Model\Order $order
     * @return string
     */
    public function getReturnCreateUrl($order)
    {
        if ($this->_customerSession->isLoggedIn()) {
            return $this->_getUrl('orderticket/returns/create', ['order_id' => $order->getId()]);
        } else {
            return $this->_getUrl('orderticket/guest/create', ['order_id' => $order->getId()]);
        }
    }

    /**
     * Get foordertickettted return address
     *
     * @param string $foordertickettCode
     * @param array $data - array of address data
     * @param int|null $storeId - Store Id
     * @return string
     */
    public function getReturnAddress($formatCode = 'html', $data = [], $storeId = null)
    {
        if (empty($data)) {
            $data = $this->getReturnAddressData($storeId);
        }

        $format = null;

        if (isset($data['countryId'])) {
            $countryModel = $this->_getCountryModel()->load($data['countryId']);
            $format = $countryModel->getFormat($formatCode);
        }

        if (!$format) {
            $path = sprintf('%s%s', \Magento\Customer\Model\Address\Config::XML_PATH_ADDRESS_TEMPLATE, $formatCode);
            $format = $this->scopeConfig->getValue(
                $path,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            );
        }

        return $this->_filterManager->template($format, ['variables' => $data]);
    }

    /**
     * Get return contact name
     *
     * @param int|null $storeId
     * @return \Magento\Framework\DataObject
     */
    public function getReturnContactName($storeId = null)
    {
        $contactName = new \Magento\Framework\DataObject();
        if ($this->scopeConfig->isSetFlag(
            OrderTicket::XML_PATH_USE_STORE_ADDRESS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        )
        ) {
            $admin = $this->_authSession->getUser();
            $contactName->setFirstName($admin->getFirstname());
            $contactName->setLastName($admin->getLastname());
            $contactName->setName($admin->getName());
        } else {
            $name = $this->scopeConfig->getValue(
                Shipping::XML_PATH_CONTACT_NAME,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            );
            $contactName->setFirstName('');
            $contactName->setLastName($name);
            $contactName->setName($name);
        }
        return $contactName;
    }

    /**
     * Get return address model
     *
     * @param int|null $storeId
     * @return \Magento\Quote\Model\Quote\Address
     */
    public function getReturnAddressModel($storeId = null)
    {
        /** @var $addressModel \Magento\Quote\Model\Quote\Address */
        $addressModel = $this->_addressFactory->create();
        $addressModel->setData($this->getReturnAddressData($storeId));
        $addressModel->setCountryId($addressModel->getData('countryId'));
        $addressModel->setStreet($addressModel->getData('street1') . "\n" . $addressModel->getData('street2'));
        return $addressModel;
    }

    /**
     * Get return address array depending on config settings
     *
     * @param \Magento\Store\Model\Store|null|int $store
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function getReturnAddressData($store = null)
    {
        if (!$store) {
            $store = $this->_storeManager->getStore();
        }

        if ($this->scopeConfig->isSetFlag(
            OrderTicket::XML_PATH_USE_STORE_ADDRESS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        )
        ) {
            $data = [
                'city' => $this->scopeConfig->getValue(
                    \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_CITY,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $store
                ),
                'countryId' => $this->scopeConfig->getValue(
                    \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_COUNTRY_ID,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $store
                ),
                'postcode' => $this->scopeConfig->getValue(
                    \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_ZIP,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $store
                ),
                'region_id' => $this->scopeConfig->getValue(
                    \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_REGION_ID,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $store
                ),
                'street2' => $this->scopeConfig->getValue(
                    \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_ADDRESS2,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $store
                ),
                'street1' => $this->scopeConfig->getValue(
                    \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_ADDRESS1,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $store
                )
            ];
        } else {
            $data = [
                'city' => $this->scopeConfig->getValue(
                    Shipping::XML_PATH_CITY,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $store
                ),
                'countryId' => $this->scopeConfig->getValue(
                    Shipping::XML_PATH_COUNTRY_ID,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $store
                ),
                'postcode' => $this->scopeConfig->getValue(
                    Shipping::XML_PATH_ZIP,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $store
                ),
                'region_id' => $this->scopeConfig->getValue(
                    Shipping::XML_PATH_REGION_ID,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $store
                ),
                'street2' => $this->scopeConfig->getValue(
                    Shipping::XML_PATH_ADDRESS2,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $store
                ),
                'street1' => $this->scopeConfig->getValue(
                    Shipping::XML_PATH_ADDRESS1,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $store
                ),
                'firstname' => $this->scopeConfig->getValue(
                    Shipping::XML_PATH_CONTACT_NAME,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $store
                )
            ];
        }

        $data['country'] = !empty($data['countryId']) ? $this->_countryFactory->create()->loadByCode(
            $data['countryId']
        )->getName() : '';
        $region = $this->_regionFactory->create()->load($data['region_id']);
        $data['region_id'] = $region->getCode();
        $data['region'] = $region->getName();
        $data['company'] = $this->scopeConfig->getValue(
            \Magento\Store\Model\Information::XML_PATH_STORE_INFO_NAME,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        $data['telephone'] = $this->scopeConfig->getValue(
            \Magento\Store\Model\Information::XML_PATH_STORE_INFO_PHONE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        $data['email'] = $this->scopeConfig->getValue(
            \Designnbuy\OrderTicket\Model\Config::XML_PATH_EMAIL_COPY_TO,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );

        return $data;
    }

    /**
     * Get Country model
     *
     * @return \Magento\Directory\Model\Country
     */
    protected function _getCountryModel()
    {
        if ($this->_countryModel === null) {
            $this->_countryModel = $this->_countryFactory->create();
        }
        return $this->_countryModel;
    }

    /**
     * Get Contact Email Address title
     *
     * @return string
     */
    public function getContactEmailLabel()
    {
        return __('Contact Email Address');
    }

    /**
     * Get key=>value array of "big four" shipping carriers with store-defined labels
     *
     * @param int|\Magento\Store\Model\Store|null $store
     * @return array
     */
    public function getShippingCarriers($store = null)
    {
        $carriers = [];
        foreach ($this->carrierHelper->getOnlineCarrierCodes($store) as $carrierCode) {
            $carriers[$carrierCode] = $this->carrierHelper->getCarrierConfigValue($carrierCode, 'title', $store);
        }
        return $carriers;
    }

    /**
     * Get key=>value array of enabled in website and enabled for ORDERTICKET shipping carriers
     * from "big four" with their store-defined labels
     *
     * @param int|\Magento\Store\Model\Store|null $store
     * @return array
     */
    public function getAllowedShippingCarriers($store = null)
    {
        $allowedCarriers = $this->getShippingCarriers($store);
        foreach (array_keys($allowedCarriers) as $carrier) {
            if (!$this->carrierHelper->getCarrierConfigValue($carrier, 'active_orderticket', $store)) {
                unset($allowedCarriers[$carrier]);
            }
        }
        return $allowedCarriers;
    }

    /**
     * Retrieve carrier
     *
     * @param string $code Shipping method code
     * @param int|int[] $storeId
     * @return bool|\Magento\Shipping\Model\Carrier\AbstractCarrierOnline
     */
    public function getCarrier($code, $storeId = null)
    {
        $data = explode('_', $code, 2);
        $carrierCode = $data[0];

        if (!$this->carrierHelper->getCarrierConfigValue($carrierCode, 'active_orderticket', $storeId)) {
            return false;
        }
        return $this->_carrierFactory->create($carrierCode, $storeId);
    }

    /**
     * Shipping package popup URL getter
     *
     * @param OrderTicket $model
     * @param string $action string
     * @return string
     */
    public function getPackagePopupUrlByOrderTicketModel($model, $action = 'package')
    {
        $key = 'orderticket_id';
        $method = 'getId';
        $param = [
            'hash' => $this->urlEncoder->encode("{$key}:{$model->{$method}()}:{$model->getProtectCode()}")
        ];

        $storeId = is_object($model) ? $model->getStoreId() : null;
        $storeModel = $this->_storeManager->getStore($storeId);
        return $storeModel->getUrl('orderticket/tracking/' . $action, $param);
    }

    /**
     * Shipping tracking popup URL getter
     *
     * @param OrderTicket|Shipping $track
     * @return string
     */
    public function getTrackingPopupUrlBySalesModel($track)
    {
        if ($track instanceof OrderTicket) {
            return $this->_getTrackingUrl('orderticket_id', $track);
        } elseif ($track instanceof Shipping) {
            return $this->_getTrackingUrl('track_id', $track, 'getEntityId');
        }
    }

    /**
     * Retrieve tracking url with params
     *
     * @param  string $key
     * @param  Shipping|OrderTicket $model
     * @param  string $method - option
     * @return string
     */
    protected function _getTrackingUrl($key, $model, $method = 'getId')
    {
        $urlPart = "{$key}:{$model->{$method}()}:{$model->getProtectCode()}";
        $params = [
            '_direct' => 'orderticket/tracking/popup',
            '_query' => ['hash' => $this->urlEncoder->encode($urlPart)]
        ];

        $storeModel = $this->_storeManager->getStore($model->getStoreId());
        return $storeModel->getUrl('', $params);
    }

    /**
     * Decode url hash
     *
     * @param  string $hash
     * @return array
     */
    public function decodeTrackingHash($hash)
    {
        $hash = explode(':', $this->urlDecoder->decode($hash));
        if (count($hash) === 3 && in_array($hash[0], $this->_allowedHashKeys)) {
            return ['key' => $hash[0], 'id' => (int)$hash[1], 'hash' => $hash[2]];
        }
        return [];
    }

    /**
     * Get foorderticketted date in store timezone
     *
     * @param string $date
     * @return string
     */
    public function getFormatedDate($date)
    {
        $date = new \DateTime($date);

        $date->setTimezone(
            new \DateTimeZone(
                $this->_localeDate->getConfigTimezone(null, $this->_storeManager->getStore())
            )
        );

        return $this->_localeDate->formatDate($date, \IntlDateFormatter::SHORT);
    }

    /**
     * Retrieves ORDERTICKET item name for backend
     *
     * @param \Magento\Sales\Model\Order\Item $item
     * @return string
     */
    public function getAdminProductName($item)
    {
        $name = $item->getName();
        $result = [];
        if ($options = $item->getProductOptions()) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }

            if (!empty($result)) {
                $implode = [];
                foreach ($result as $val) {
                    $implode[] = isset($val['print_value']) ? $val['print_value'] : $val['value'];
                }
                return $name . ' (' . implode(', ', $implode) . ')';
            }
        }
        return $name;
    }

    /**
     * Retrieves ORDERTICKET item sku for backend
     *
     * @param  \Magento\Sales\Model\Order\Item $item
     * @return string
     */
    public function getAdminProductSku($item)
    {
        return $this->adminOrderItem->getSku($item);
    }

    /**
     * Parses quantity depending on isQtyDecimal flag
     *
     * @param float $quantity
     * @param \Designnbuy\OrderTicket\Model\Item $item
     * @return int|float
     */
    public function parseQuantity($quantity, $item)
    {
        if ($quantity === null) {
            $quantity = $item->getOrigData('qty_requested');
        }
        if ($item->getIsQtyDecimal()) {
            return sprintf("%01.4f", $quantity);
        } else {
            return intval($quantity);
        }
    }

    /**
     * Get Qty by status
     *
     * @param \Designnbuy\OrderTicket\Model\Item $item
     * @return int|float
     */
    public function getQty($item)
    {
        $qty = $item->getQtyRequested();

        if ($item->getQtyApproved() && $item->getStatus() == \Designnbuy\OrderTicket\Model\OrderTicket\Source\Status::STATE_APPROVED) {
            $qty = $item->getQtyApproved();
        } elseif ($item->getQtyReturned() &&
            ($item->getStatus() == \Designnbuy\OrderTicket\Model\OrderTicket\Source\Status::STATE_RECEIVED ||
            $item->getStatus() == \Designnbuy\OrderTicket\Model\OrderTicket\Source\Status::STATE_REJECTED)
        ) {
            $qty = $item->getQtyReturned();
        } elseif ($item->getQtyAuthorized() &&
            $item->getStatus() == \Designnbuy\OrderTicket\Model\OrderTicket\Source\Status::STATE_AUTHORIZED
        ) {
            $qty = $item->getQtyAuthorized();
        }

        return $this->parseQuantity($qty, $item);
    }

    public function getFilePath()
    {
        return $this->_mediaDirectory->getAbsolutePath(self::FILE_PATH);
    }
}
