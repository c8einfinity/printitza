<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Designnbuy\Vendor\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;


class AfterOrderPlace implements ObserverInterface
{
    /**
     * Subtract qtys of quote item products after multishipping checkout
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        $order = $observer->getEvent()->getData('order');
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        if($order){
            // Get the items from the order
            $items = $order->getAllVisibleItems();
            foreach ($items as $item) {
                if($item->getVendorId()){
                    
                    $vendor = $this->_objectManager->create('Magento\User\Model\User')->load($item->getVendorId());
                    if(!empty($vendor)){
                        $order->setCustomerEmail($vendor->getEmail());
                        $this->_objectManager->create('\Magento\Sales\Model\OrderNotifier')
                        ->notify($order);
                    }
                }

            }
        }

        return $this;
    }
}
