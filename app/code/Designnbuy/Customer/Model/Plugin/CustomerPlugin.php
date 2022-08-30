<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Customer\Model\Plugin;

use Magento\Customer\Api\CustomerRepositoryInterface as CustomerRepository;
use Magento\Customer\Api\Data\CustomerInterface;
use Designnbuy\Customer\Model\DesignFactory;

class CustomerPlugin
{
    /**
     * Factory used for manipulating customer subscriptions
     *
     * @var DesignFactory
     */
    private $designFactory;

    /**
     * Initialize dependencies.
     *
     * @param DesignFactory $designFactory
     */
    public function __construct(DesignFactory $designFactory)
    {
        $this->designFactory = $designFactory;
    }

    /**
     * Plugin after create customer that updates any customer subscription that may have existed.
     *
     * @param CustomerRepository $subject
     * @param CustomerInterface $customer
     * @return CustomerInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterSave(CustomerRepository $subject, CustomerInterface $customer)
    {
        $this->designFactory->create()->updateSubscription($customer->getId());
        return $customer;
    }

    /**
     * Plugin around customer repository save. If we have extension attribute (is_subscribed) we need to subscribe that customer
     *
     * @param CustomerRepository $subject
     * @param \Closure $proceed
     * @param CustomerInterface $customer
     * @param null $passwordHash
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundSave(
        CustomerRepository $subject,
        \Closure $proceed,
        CustomerInterface $customer,
        $passwordHash = null
    ) {
        /** @var CustomerInterface $savedCustomer */
        $savedCustomer = $proceed($customer, $passwordHash);

        if ($savedCustomer->getId() && $customer->getExtensionAttributes()) {
            if ($customer->getExtensionAttributes()->getIsSubscribed() === true) {
                $this->designFactory->create()->subscribeCustomerById($savedCustomer->getId());
            } elseif ($customer->getExtensionAttributes()->getIsSubscribed() === false) {
                $this->designFactory->create()->unsubscribeCustomerById($savedCustomer->getId());
            }
        }

        return $savedCustomer;
    }
    
    /**
     * Plugin around delete customer that updates any customer subscription that may have existed.
     *
     * @param CustomerRepository $subject
     * @param callable $deleteCustomerById Function we are wrapping around
     * @param int $customerId Input to the function
     * @return bool
     */
    public function aroundDeleteById(
        CustomerRepository $subject,
        callable $deleteCustomerById,
        $customerId
    ) {
        $customer = $subject->getById($customerId);
        $result = $deleteCustomerById($customerId);
        /** @var \Designnbuy\Customer\Model\Design $design */
        $design = $this->designFactory->create();
        $design->loadByEmail($customer->getEmail());
        if ($design->getId()) {
            $design->delete();
        }
        return $result;
    }

    /**
     * Plugin around delete customer that updates any customer subscription that may have existed.
     *
     * @param CustomerRepository $subject
     * @param callable $deleteCustomer Function we are wrapping around
     * @param CustomerInterface $customer Input to the function
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundDelete(
        CustomerRepository $subject,
        callable $deleteCustomer,
        $customer
    ) {
        $result = $deleteCustomer($customer);
        /** @var \Designnbuy\Customer\Model\Design $design */
        $design = $this->designFactory->create();
        $design->loadByEmail($customer->getEmail());
        if ($design->getId()) {
            $design->delete();
        }
        return $result;
    }
}
