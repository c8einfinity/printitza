<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Observer;

use BrainActs\SalesRepresentative\Model\ConfigFactory;
use BrainActs\SalesRepresentative\Model\Convert;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class CheckoutSubmitAllAfterObserver
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class CheckoutSubmitAllAfterObserver implements ObserverInterface
{

    /**
     * @var Convert
     */
    private $convert;

    /**
     * @var ConfigFactory
     */
    private $configFactory;

    /**
     * CheckoutSubmitAllAfterObserver constructor.
     * @param Convert $convert
     * @param ConfigFactory $configFactory
     */
    public function __construct(Convert $convert, ConfigFactory $configFactory)
    {
        $this->convert = $convert;
        $this->configFactory = $configFactory;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @throws \Exception
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \BrainActs\SalesRepresentative\Model\Config $config */
        $config = $this->configFactory->create();
        $order = $observer->getEvent()->getData('order');
        $quote = $observer->getEvent()->getData('quote');

        if ($config->isActiveFront() && !$config->isEnabledChoose()) {
            $this->convert->apply($order->getId());
        } elseif ($config->isActiveFront() && $config->isEnabledChoose()) {
            $memberId = $quote->getSalesRepresentativeId();
            if (empty($memberId)) {
                $this->convert->apply($order->getId());
            } else {
                $this->convert->applyCustomerToMember($memberId, $quote->getCustomerId(), $order);
            }
        }
    }
}
