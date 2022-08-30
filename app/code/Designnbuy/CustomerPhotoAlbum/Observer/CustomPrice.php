<?php
    
namespace Designnbuy\CustomerPhotoAlbum\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;

class CustomPrice implements ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer) {
        $item = $observer->getEvent()->getData('quote_item');			
        $item = ( $item->getParentItem() ? $item->getParentItem() : $item );
        $infoBuyRequest = $item->getOptionByCode('info_buyRequest');
        $buyRequest = json_decode($infoBuyRequest->getValue(),true);
        
        /* $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/test.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info(print_r($buyRequest, true)); */

        
        if (array_key_exists("photoAlbum",$buyRequest) && $buyRequest['photoAlbum'] == 1 && array_key_exists("selected_custom_price",$buyRequest)){
            $item->setCustomPrice($buyRequest['selected_custom_price']);
            $item->setOriginalCustomPrice($buyRequest['selected_custom_price']);
            $item->getProduct()->setIsSuperMode(true);
        }
        
    }

}

?>