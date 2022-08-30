<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Observer\Adminhtml;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class CheckoutSubmitAllAfterObserver
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class CheckoutSubmitAllAfterObserver implements ObserverInterface
{

    /**
     * @var \BrainActs\SalesRepresentative\Model\Convert|null
     */
    private $convert = null;

    /**
     * @var \BrainActs\SalesRepresentative\Model\Config
     */
    private $config;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    private $session;

    /**
     * CheckoutSubmitAllAfterObserver constructor.
     * @param \BrainActs\SalesRepresentative\Model\Convert $convert
     * @param \BrainActs\SalesRepresentative\Model\ConfigFactory $configFactory
     */
    public function __construct(
        \BrainActs\SalesRepresentative\Model\Convert $convert,
        \BrainActs\SalesRepresentative\Model\ConfigFactory $configFactory,
        \Magento\Backend\Model\Auth\Session $adminSession
    ) {
        $this->session = $adminSession;
        $this->convert = $convert;
        $this->config = $configFactory->create();
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Exception
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->config->isAutoAssignAdmin()) {
            $order = $observer->getEvent()->getData('order');
            $this->convert->autoAssignAdmin($order->getId(), $this->session->getUser()->getId());
        }
    }
}
