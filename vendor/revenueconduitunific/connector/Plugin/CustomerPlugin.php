<?php

namespace Unific\Connector\Plugin;

use Magento\Customer\Api\CustomerRepositoryInterface as CustomerRepository;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Store\Model\App\Emulation;
use Unific\Connector\Helper\Data\Customer;
use Unific\Connector\Helper\Hmac;
use Unific\Connector\Helper\Message\Queue;
use Unific\Connector\Helper\Settings;

class CustomerPlugin extends AbstractPlugin
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
     * @var RequestInterface
     */
    protected $request;
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
     * @param RequestInterface $request
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
        RequestInterface $request,
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
        $this->request = $request;
        $this->subscriberFactory = $subscriberFactory;
    }

    public function beforeSave()
    {
        $this->customerDataHelper->setSavingCustomer(true);
    }

    /**
     *
     * @param CustomerRepository $subject
     * @param CustomerInterface $result
     * @param CustomerInterface $customer
     * @return CustomerInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws LocalizedException
     */
    public function afterSave(CustomerRepository $subject, CustomerInterface $result, CustomerInterface $customer)
    {
        if ($this->scopeConfig->getValue('unific/connector/enabled') == 1 && $result->getId() > 0) {
            $webhookSubject = 'customer/create';

            // Only send if the date created is more than 5 seconds different from the updated date
            // Sometimes a second save action happens within a second after creation
            if ($result->getUpdatedAt() != ""
                && strtotime($result->getUpdatedAt()) - strtotime($result->getCreatedAt()) > 5
            ) {
                $webhookSubject = 'customer/update';
            }

            $this->customerDataHelper->setCustomer($result);

            if (filter_var($result->getEmail(), FILTER_VALIDATE_EMAIL)) {
                $subscriber = $this->subscriberFactory->create()->loadByEmail($result->getEmail());
                $this->customerDataHelper->setOptionNewsletter(
                    $this->request->getParam('is_subscribed', $subscriber->isSubscribed())
                );

                $this->processWebhook(
                    $this->customerDataHelper->getCustomerInfo(),
                    $this->scopeConfig->getValue('unific/webhook/customer_endpoint'),
                    Settings::PRIORITY_CUSTOMER,
                    $webhookSubject
                );
            }
        }

        $this->customerDataHelper->setSavingCustomer(false);

        return $result;
    }
}
