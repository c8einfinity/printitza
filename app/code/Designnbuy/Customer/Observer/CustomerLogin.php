<?php
namespace Designnbuy\Customer\Observer;

use Magento\Framework\Event\ObserverInterface;

class CustomerLogin implements ObserverInterface
{
	protected $cacheManager;

    public function __construct(
        \Magento\Framework\App\Cache\Manager $cacheManager
    ) {
        $this->cacheManager = $cacheManager;
    }


    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->cacheManager->clean($this->cacheManager->getAvailableTypes());
        $this->cacheManager->flush($this->cacheManager->getAvailableTypes());
    }
}
