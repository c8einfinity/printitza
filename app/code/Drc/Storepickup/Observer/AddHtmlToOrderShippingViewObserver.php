<?php
namespace Drc\Storepickup\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class AddHtmlToOrderShippingViewObserver implements ObserverInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function execute(EventObserver $observer)
    {
        if ($observer->getElementName() == 'order_shipping_view') {
            $orderShippingViewBlock = $observer->getLayout()->getBlock($observer->getElementName());
            $order = $orderShippingViewBlock->getOrder();
            $localeDate = $this->objectManager->create('\Magento\Framework\Stdlib\DateTime\TimezoneInterface');
            if ($order->getDeliveryDate()!= '' && $order->getDeliveryDate() != '0000-00-00 00:00:00') {
                $formattedDate = $localeDate->formatDateTime(
                    $order->getDeliveryDate(),
                    \IntlDateFormatter::MEDIUM,
                    \IntlDateFormatter::MEDIUM,
                    null,
                    $localeDate->getConfigTimezone(
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        $order->getStore()->getCode()
                    )
                );
            } else {
                $formattedDate = __('N/A');
            }

            $storeList = __('N/A');
            if($order->getStoreList()){
                $storeList = $order->getStoreList();
            }

            $deliveryDateBlock = $this->objectManager->create('Magento\Framework\View\Element\Template');
            $deliveryDateBlock->setDeliveryDate($formattedDate);
            $deliveryDateBlock->setStoreList($storeList);
            $deliveryDateBlock->setTemplate('Drc_Storepickup::order_info_shipping_info.phtml');
            $html = $observer->getTransport()->getOutput() . $deliveryDateBlock->toHtml();
            $observer->getTransport()->setOutput($html);
        }
    }
}
