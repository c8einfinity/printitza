<?php

namespace Unific\Connector\Plugin;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Newsletter\Model\Subscriber;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\StoreManagerInterface;
use Unific\Connector\Helper\Data\Customer;
use Unific\Connector\Helper\Hmac;
use Unific\Connector\Helper\Message\Queue;
use Unific\Connector\Helper\Settings;

class SubscriberPlugin extends AbstractPlugin
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
     * @var StoreManagerInterface
     */
    protected $storeManager;

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
     * @param StoreManagerInterface $storeManager
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
        Emulation $emulation,
        StoreManagerInterface $storeManager
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
        $this->storeManager = $storeManager;
    }

    /**
     *
     * @param Subscriber $subject
     * @return Subscriber
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterSave(Subscriber $subject)
    {
        if ($this->scopeConfig->getValue('unific/connector/enabled') == 1 && $subject->getCustomerId() > 0) {
            $webhookSubject = 'customer/update';

            try {
                $websiteId = $this->storeManager->getStore($subject->getStoreId())->getWebsiteId();
                $customer = $this->customerRepository->get($subject->getSubscriberEmail(), $websiteId);
            } catch (LocalizedException $e) {
                // an error in customer_id field in customer data
                return;
            }

            $this->customerDataHelper->setCustomer($customer);

            if (filter_var($subject->getEmail(), FILTER_VALIDATE_EMAIL)) {
                $this->customerDataHelper->setOptionNewsletter(
                    $this->request->getParam('is_subscribed', $subject->isSubscribed())
                );

                $this->processWebhook(
                    $this->customerDataHelper->getCustomerInfo(),
                    $this->scopeConfig->getValue('unific/webhook/customer_endpoint'),
                    Settings::PRIORITY_CUSTOMER,
                    $webhookSubject
                );
            }
        }

        return $subject;
    }
}
