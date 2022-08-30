<?php

namespace Unific\Connector\Helper\Data;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Customer\Model\AddressFactory;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\GroupManagement;
use Magento\Customer\Model\ResourceModel\Address\CollectionFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\Api\ExtensibleDataInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Quote\Model\Quote\Address;
use Unific\Connector\Helper\Filter;

class Customer
{
    /**
     * @var Filter
     */
    protected $filterHelper;
    /**
     * @var ExtensibleDataInterface
     */
    protected $guestBillingAddress;
    /**
     * @var ExtensibleDataInterface
     */
    protected $guestShippingAddress;
    /**
     * @var Session
     */
    protected $customerSession;
    /**
     * @var CustomerFactory
     */
    protected $customerFactory;
    /**
     * @var AddressFactory
     */
    protected $addressFactory;
    /**
     * @var CollectionFactory
     */
    protected $addressCollectionFactory;
    /**
     * @var SubscriberFactory
     */
    protected $subscriberFactory;
    /**
     * @var CustomerInterface
     */
    protected $customer;
    /**
     * @var Formatter
     */
    private $dataFormatter;
    /**
     * @var GroupRepositoryInterface
     */
    private $groupRepository;

    /**
     * @var Converter
     */
    private $outputProcessor;

    /**
     * @var bool
     */
    protected $savingCustomer = false;
    /**
     * @var array
     */
    protected $returnData = [];
    /**
     * @var array
     */
    protected $quoteAddresses = [];

    /**
     * OrderPlugin constructor.
     * @param Filter $filterHelper
     * @param Session $customerSession
     * @param CustomerFactory $customerFactory
     * @param AddressFactory $addressFactory
     * @param CollectionFactory $addressCollectionFactory
     * @param SubscriberFactory $subscriberFactory
     * @param Formatter $dataFormatter
     * @param GroupRepositoryInterface $groupRepository
     * @param Converter $outputProcessor
     */
    public function __construct(
        Filter $filterHelper,
        Session $customerSession,
        CustomerFactory $customerFactory,
        AddressFactory $addressFactory,
        CollectionFactory $addressCollectionFactory,
        SubscriberFactory $subscriberFactory,
        Formatter $dataFormatter,
        GroupRepositoryInterface $groupRepository,
        Converter $outputProcessor
    ) {
        $this->filterHelper = $filterHelper;
        $this->customerSession = $customerSession;
        $this->customerFactory = $customerFactory;
        $this->addressFactory = $addressFactory;
        $this->addressCollectionFactory = $addressCollectionFactory;
        $this->dataFormatter = $dataFormatter;
        $this->subscriberFactory = $subscriberFactory;
        $this->groupRepository = $groupRepository;
        $this->outputProcessor = $outputProcessor;
    }

    /**
     * @param CustomerInterface $customer
     * @throws LocalizedException
     */
    public function setCustomer(CustomerInterface $customer)
    {
        $this->customer = $customer;
        $this->setCustomerInfo();
    }

    /**
     * @return CustomerInterface
     * @throws LocalizedException
     */
    public function getCustomer()
    {
        if ($this->customer == null && $this->customerSession->isLoggedIn() === true) {
            $this->setCustomer($this->customerSession->getCustomer()->getDataModel());
        }

        return $this->customer;
    }

    /**
     * @return bool
     */
    public function isSavingCustomer()
    {
        return $this->savingCustomer;
    }

    /**
     * @param bool $flag
     */
    public function setSavingCustomer($flag)
    {
        $this->savingCustomer = (bool) $flag;
    }

    /**
     * @param $entity
     * @throws LocalizedException
     */
    public function generateGuestCustomer($entity)
    {
        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = $this->customerFactory->create();

        // Using the billing address because the item might not need shipping
        if ($entity->getBillingAddress() != null) {
            $customer->setEmail($entity->getBillingAddress()->getEmail());
            $customer->setPrefix($entity->getBillingAddress()->getPrefix());
            $customer->setFirstname($entity->getBillingAddress()->getFirstname());
            $customer->setMiddlename($entity->getBillingAddress()->getMiddlename());
            $customer->setLastname($entity->getBillingAddress()->getLastname());
            $customer->setSuffix($entity->getBillingAddress()->getSuffix());
            $customer->setDob($entity->getBillingAddress()->getDob());
            $customer->setGender($entity->getBillingAddress()->getGender());
            $customer->setGroupId(GroupManagement::NOT_LOGGED_IN_ID);

            $this->guestBillingAddress = ($entity->getBillingAddress()) ?
                $entity->getBillingAddress() : $entity->getShippingAddress();
            $this->guestShippingAddress = ($entity->getShippingAddress()) ?
                $entity->getShippingAddress() : $entity->getBillingAddress();
        }

        $this->setCustomer($customer->getDataModel());
    }

    /**
     * @throws LocalizedException
     */
    protected function setCustomerInfo()
    {
        $this->returnData = $this->outputProcessor->convertValue($this->customer, CustomerInterface::class);
        unset($this->returnData['addresses']);

        if ($this->customer->getId() === null) {
            $this->setGuestAddresses();
        } else {
            $this->returnData['entity_id'] = $this->customer->getId();
            $this->returnData['customer_id'] = $this->customer->getId();
            $this->setCustomerAddresses();
        }

        if (count($this->quoteAddresses) > 0) {
            $this->returnData['addresses'] = $this->quoteAddresses;
        }

        $this->returnData['customer_is_guest'] = (int)($this->customer->getId() === null);
        $this->returnData['contact_group'] = $this->getCustomerGroupName(
            $this->customer->getGroupId() ?: GroupManagement::NOT_LOGGED_IN_ID
        );

        $newExtensionAttributes = $this->customer->getExtensionAttributes();
        if ($newExtensionAttributes && $newExtensionAttributes->getIsSubscribed() != null
        ) {
            $this->setOptionNewsletter($newExtensionAttributes->getIsSubscribed());
        } elseif ($this->customer->getEmail()) {
            $subscriber = $this->subscriberFactory->create()->loadByEmail($this->customer->getEmail());
            $this->setOptionNewsletter($subscriber->isSubscribed() ? 1 : 0);
        } else {
            $this->setOptionNewsletter(0);
        }
    }

    /**
     * @param int $option
     */
    public function setOptionNewsletter($option = 0)
    {
        $this->returnData['optin_newsletter'] = (int)$option;
    }

    /**
     * @return array
     */
    public function getCustomerInfo()
    {
        return $this->filterHelper->sanitizeAddressData($this->returnData);
    }

    /**
     * Set the addresses based on guest data
     */
    public function setGuestAddresses()
    {
        if ($this->guestBillingAddress) {
            $this->returnData['addresses']['billing'] = $this->guestBillingAddress->getData();
            $this->returnData['addresses']['billing']['street'] = $this->guestBillingAddress->getStreetFull();
            $this->returnData['addresses']['billing'] = $this->dataFormatter->setStreetData(
                $this->returnData['addresses']['billing'],
                $this->guestBillingAddress->getStreetFull()
            );
        }

        if ($this->guestShippingAddress) {
            $this->returnData['addresses']['shipping'] = $this->guestShippingAddress->getData();
            $this->returnData['addresses']['shipping']['street'] = $this->guestBillingAddress->getStreetFull();

            $this->returnData['addresses']['shipping'] = $this->dataFormatter->setStreetData(
                $this->returnData['addresses']['shipping'],
                $this->guestShippingAddress->getStreetFull()
            );

        }
    }

    /**
     * Override default customer addresses when preparing data for cart
     *
     * @param Address $address
     * @param string $type
     */
    public function setQuoteAddress(Address $address, $type = 'billing')
    {
        $this->quoteAddresses[$type] = $address->getData();
        $this->quoteAddresses[$type]['street'] = $address->getStreetFull();

        $this->quoteAddresses[$type] = $this->dataFormatter->setStreetData(
            $this->quoteAddresses[$type],
            $address->getStreetFull()
        );

        if (isset($this->returnData['addresses'])) {
            $this->returnData['addresses'][$type] = $this->quoteAddresses[$type];
        }
    }

    /**
     * Set the address of a logged in customer
     */
    protected function setCustomerAddresses()
    {
        $billingAddressId = $this->customer->getDefaultBilling();
        $shippingAddressId = $this->customer->getDefaultShipping();

        //billing
        if ($billingAddressId) {
            $billingAddress = $this->addressFactory->create()->load($billingAddressId);
            $this->returnData['addresses']['billing'] = $billingAddress->getData();
            $this->returnData['addresses']['billing']['street'] = $billingAddress->getStreetFull();

            $this->returnData['addresses']['billing'] = $this->dataFormatter->setStreetData(
                $this->returnData['addresses']['billing'],
                $billingAddress->getStreetFull()
            );

            if ($shippingAddressId === null) {
                $this->returnData['addresses']['shipping'] = $billingAddress->getData();
                $this->returnData['addresses']['shipping']['street'] = $billingAddress->getStreetFull();
                $this->returnData['addresses']['shipping'] = $this->dataFormatter->setStreetData(
                    $this->returnData['addresses']['shipping'],
                    $billingAddress->getStreetFull()
                );
            }
        }

        if ($shippingAddressId) {
            $shippingAddress = $this->addressFactory->create()->load($shippingAddressId);
            $this->returnData['addresses']['shipping'] = $shippingAddress->getData();
            $this->returnData['addresses']['shipping']['street'] = $shippingAddress->getStreetFull();

            $this->returnData['addresses']['shipping'] = $this->dataFormatter->setStreetData(
                $this->returnData['addresses']['shipping'],
                $shippingAddress->getStreetFull()
            );

            if ($billingAddressId === null) {
                $this->returnData['addresses']['billing'] = $shippingAddress->getData();
                $this->returnData['addresses']['billing']['street'] = $shippingAddress->getStreetFull();

                $this->returnData['addresses']['billing'] = $this->dataFormatter->setStreetData(
                    $this->returnData['addresses']['billing'],
                    $shippingAddress->getStreetFull()
                );

            }
        }
    }

    /**
     * Try fetch group data and return group code
     *
     * @param int $groupId
     * @return string|null
     * @throws LocalizedException
     */
    protected function getCustomerGroupName($groupId = null)
    {
        try {
            $groupId = $groupId ?: $this->customer->getGroupId();
            if ($customerGroup = $this->groupRepository->getById($groupId)) {
                return $customerGroup->getCode();
            }
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }
}
