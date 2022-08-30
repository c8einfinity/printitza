<?php

namespace Unific\Connector\Plugin;

use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Store\Model\App\Emulation;
use Unific\Connector\Helper\Data\Customer;
use Unific\Connector\Helper\Hmac;
use Unific\Connector\Helper\Message\Queue;
use Unific\Connector\Helper\Settings;

class CustomerAddressPlugin extends AbstractPlugin
{
    /**
     * @var Customer
     */
    protected $customerDataHelper;
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;
    /**
     * @var SubscriberFactory
     */
    protected $subscriberFactory;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param Hmac $hmacHelper
     * @param Queue $queueHelper
     * @param ProductMetadataInterface $productMetadata
     * @param ModuleListInterface $moduleList
     * @param Customer $customerDataHelper
     * @param CustomerRepositoryInterface $customerRepository
     * @param SubscriberFactory $subscriberFactory
     * @param Emulation $emulation
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Hmac $hmacHelper,
        Queue $queueHelper,
        ProductMetadataInterface $productMetadata,
        ModuleListInterface $moduleList,
        Customer $customerDataHelper,
        CustomerRepositoryInterface $customerRepository,
        SubscriberFactory $subscriberFactory,
        Emulation $emulation
    ) {
        parent::__construct(
            $scopeConfig,
            $hmacHelper,
            $queueHelper,
            $productMetadata,
            $moduleList,
            $emulation
        );

        $this->customerDataHelper = $customerDataHelper;
        $this->customerRepository = $customerRepository;
        $this->subscriberFactory = $subscriberFactory;
    }

    /**
     * @param AddressRepositoryInterface $subject
     * @param AddressInterface $result
     * @param AddressInterface $address
     * @return AddressInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function afterSave(AddressRepositoryInterface $subject, AddressInterface $result, AddressInterface $address)
    {
        if ($this->scopeConfig->getValue('unific/connector/enabled') == 1 && $result->getCustomerId() > 0) {
            if (!$this->isAddressDefault($result) && !$this->isAddressDefault($address)) {
                // do not send hook if saved address is not default
                // we are checking the result to see if address has been already default
                // if default flag has changed to true that information is not in $result but in function argument
                // as default flag cannot be changed from true to false via edit form there is no risk of detecting
                // default flag removal
                return $result;
            }
            if ($this->customerDataHelper->isSavingCustomer()) {
                // save address during customer main save, we skip it as data are already prepared
                return $result;
            }

            $customer = $this->customerRepository->getById($result->getCustomerId());
            if ($address->isDefaultShipping()) {
                $customer->setDefaultShipping($result->getId());
            }
            if ($address->isDefaultBilling()) {
                $customer->setDefaultBilling($result->getId());
            }

            if ($customer->getId() !== null) {
                $this->customerDataHelper->setCustomer($customer);

                if (filter_var($customer->getEmail(), FILTER_VALIDATE_EMAIL)) {
                    $subscriber = $this->subscriberFactory->create()->loadByEmail($customer->getEmail());
                    $this->customerDataHelper->setOptionNewsletter($subscriber->isSubscribed());

                    $this->processWebhook(
                        $this->customerDataHelper->getCustomerInfo(),
                        $this->scopeConfig->getValue('unific/webhook/customer_endpoint'),
                        Settings::PRIORITY_CUSTOMER,
                        'customer/update'
                    );
                }
            }
        }

        return $result;
    }

    /**
     * @param AddressInterface $address
     * @return bool
     */
    private function isAddressDefault(AddressInterface $address)
    {
        if (!$address->isDefaultBilling() && !$address->isDefaultShipping()) {
            return false;
        }

        return true;
    }
}
