<?php

namespace Unific\Connector\Helper\Data;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Store\Model\StoreManagerInterface;
use Unific\Connector\Helper\Filter;

class Cart
{
    /**
     * @var Filter
     */
    protected $filterHelper;
    /**
     * @var CustomerSession
     */
    protected $customerSession;
    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;
    /**
     * @var Customer
     */
    protected $customerHelper;
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var QuoteIdMaskFactory
     */
    protected $quoteIdMaskFactory;
    /**
     * Holds a Quote API DATA object
     * @var CartInterface|Quote
     */
    private $cart;
    /**
     * @var Formatter
     */
    private $dataFormatter;
    /**
     * @var array
     */
    protected $returnData = [];

    /**
     * OrderPlugin constructor.
     * @param Filter $filterHelper
     * @param CustomerSession $customerSession
     * @param CheckoutSession $checkoutSession
     * @param Customer $customerHelper
     * @param StoreManagerInterface $storeManager
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param Formatter $dataFormatter
     */
    public function __construct(
        Filter $filterHelper,
        CustomerSession $customerSession,
        CheckoutSession $checkoutSession,
        Customer $customerHelper,
        StoreManagerInterface $storeManager,
        QuoteIdMaskFactory $quoteIdMaskFactory,
        Formatter $dataFormatter
    ) {
        $this->filterHelper = $filterHelper;
        $this->customerSession = $customerSession;
        $this->checkoutSession = $checkoutSession;
        $this->storeManager = $storeManager;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->dataFormatter = $dataFormatter;
        $this->customerHelper = $customerHelper;
    }

    /**
     * @param Quote|CartInterface $cart
     * @param null $maskedQuoteId
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function setCart(CartInterface $cart, $maskedQuoteId = null)
    {
        $this->cart = $cart;
        $this->setCartInfo($maskedQuoteId);
    }

    /**
     * @param Quote\Address $address
     */
    public function setAddressData(Quote\Address $address)
    {
        $this->returnData['customer_email'] = $address->getEmail();
        $this->returnData['customer_firstname'] = $address->getFirstname();
        $this->returnData['customer_middlename'] = $address->getMiddlename();
        $this->returnData['customer_lastname'] = $address->getLastname();
        $this->returnData['customer_prefix'] = $address->getPrefix();
        $this->returnData['customer_suffix'] = $address->getSuffix();
        $this->returnData['customer_postcode'] = $address->getPostcode();

        $this->returnData = $this->dataFormatter->setStreetData(
            $this->returnData,
            $address->getStreetFull(),
            'customer_street'
        );

        $this->returnData['customer_street'] = $address->getStreetFull();
        $this->returnData['customer_city'] = $address->getCity();
        $this->returnData['customer_telephone'] = $address->getTelephone();
        $this->returnData['customer_fax'] = $address->getFax();
        $this->returnData['customer_company'] = $address->getCompany();
        $this->returnData['customer_region'] = $address->getRegionCode();
        $this->returnData['customer_country'] = $address->getCountryModel()->getName();
    }

    /**
     * @return CartInterface
     */
    public function getCart()
    {
        return $this->cart;
    }

    /**
     * @param string $maskedQuoteId
     * @return mixed
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    protected function setCartInfo($maskedQuoteId = null)
    {
        $this->returnData = $this->filterHelper->processObjectData($this->cart->getData());

        if ($this->cart->getEntityId() != null && ctype_digit($this->cart->getEntityId())) {
            if ($maskedQuoteId == null) {
                $quoteIdMask = $this->quoteIdMaskFactory->create()->load($this->cart->getEntityId(), 'quote_id');

                $maskedQuoteId = $quoteIdMask->getMaskedId();
                if ($quoteIdMask->getMaskedId() === null) {
                    $quoteIdMask->setQuoteId($this->cart->getEntityId())->save();
                    $maskedQuoteId = $quoteIdMask->getMaskedId();
                }
            }

            $this->returnData['masked_id'] = $maskedQuoteId;

            $this->returnData['abandoned_checkout_url'] =
                $this->storeManager->getStore(
                    $this->cart->getStoreId()
                )->getUrl(
                    'unific/cart/restore',
                    [
                        'id'      => $maskedQuoteId,
                        '_nosid'  => true,
                        '_secure' => true
                    ]
                );
        }

        if ($this->customerSession->isLoggedIn()) {
            $this->returnData['customer_email'] = $this->customerSession->getCustomer()->getEmail();
            $this->returnData['customer_firstname'] = $this->customerSession->getCustomer()->getFirstname();
            $this->returnData['customer_middlename'] = $this->customerSession->getCustomer()->getMiddlename();
            $this->returnData['customer_lastname'] = $this->customerSession->getCustomer()->getLastname();
        }

        $this->returnData['items'] = [];
        foreach ($this->cart->getAllItems() as $item) {
            $this->returnData['items'][] = $this->filterHelper->processObjectData($item->getData());
        }

        // Trigger the setting of the customer
        if ($this->customerSession->isLoggedIn() === true) {
            $customerData = $this->customerSession->getCustomer()->getDataModel();
            $this->customerHelper->setCustomer($customerData);
            // by default cart is created with empty address object,
            // if city is not empty it has been filled by customer on the checkout page
            // country and postcode may be set by tax calculator or custom modules in the project
            if ($this->cart->getBillingAddress()->getCity()) {
                $this->customerHelper->setQuoteAddress($this->cart->getBillingAddress(), 'billing');
            }
            if ($this->cart->getShippingAddress()->getCity()) {
                $this->customerHelper->setQuoteAddress($this->cart->getShippingAddress(), 'shipping');
            }
            $this->returnData['customer_is_guest'] = 0;
        } else {
            $this->customerHelper->generateGuestCustomer($this->cart);
            $this->returnData['customer_is_guest'] = 1;
        }

        $customerData = $this->customerHelper->getCustomerInfo();
        if ($customerData['email'] != null) {
            $this->returnData['customer'] = $customerData;
        }
    }

    /**
     * @return array
     */
    public function getCartInfo()
    {
        // Sanitize Cart
        return $this->filterHelper->sanitizeAddressData($this->returnData);
    }
}
