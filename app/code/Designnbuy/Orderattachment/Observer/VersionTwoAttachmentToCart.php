<?php
namespace Designnbuy\Orderattachment\Observer;
 
use Magento\Framework\Event\ObserverInterface;
 
class VersionTwoAttachmentToCart implements ObserverInterface
{
    /**
     * add to cart event handler.
     *
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($observer->getRequest()->getParam('upload_params')) {
            $paramsData = [];
            $paramsData = json_decode($observer->getRequest()->getParam('upload_params'),true);
            $observer->getRequest()->setParam('upload_params','');
            $observer->getRequest()->setParams($paramsData);
            $observer->getRequest()->setParam('return_url', '');
            
            return $this;
        }
         
 
        return $this;
    }
}