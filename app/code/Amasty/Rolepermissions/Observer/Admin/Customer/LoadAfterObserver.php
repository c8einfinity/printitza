<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Observer\Admin\Customer;

use Magento\Framework\Event\ObserverInterface;

class LoadAfterObserver implements ObserverInterface
{
    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $_authorization;

    public function __construct(
        \Magento\Framework\AuthorizationInterface $authorization
    ) {
        $this->_authorization = $authorization;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customer = $observer->getCustomer();

        if (!$this->_authorization->isAllowed('Amasty_Rolepermissions::delete_customers')) {
            $customer->setIsDeleteable(false);
        }

        if (!$this->_authorization->isAllowed('Amasty_Rolepermissions::save_customers')) {
            $customer->setIsReadonly(true);
        }
    }
}
