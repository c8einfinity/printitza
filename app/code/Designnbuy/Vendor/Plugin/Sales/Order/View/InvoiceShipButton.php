<?php

namespace Designnbuy\Vendor\Plugin\Sales\Order\View;

class InvoiceShipButton
{

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    public function __construct(
        \Designnbuy\Vendor\Helper\Data $vendorData
    ) {
        $this->vendorData = $vendorData;
    }


    public function afterGetButtonList(
        \Magento\Backend\Block\Widget\Context $subject,
        $buttonList
    )
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $request = $objectManager->get('Magento\Framework\App\Action\Context')->getRequest();
        $orderId = $request->getParam('order_id');


        $currentUser = $this->vendorData->getCurrentUser();

        $canInvoice = false;
        $canShip = false;

        $order = $objectManager->create('Magento\Sales\Model\Order')
            ->load($orderId);
        //if ($order->canShip()) {
        if ($this->vendorData->getCurrentUser() && $this->vendorData->getCurrentUser()->getRole()->getRoleId() == $this->vendorData->getVendorRoleId()) {
            foreach ($order->getAllItems() as $item) {
                if ($item->getVendorId() == $currentUser->getId() && $item->canShip()){
                    $canShip = true;
                }
                if ($item->getVendorId() == $currentUser->getId() && $item->canInvoice()){
                    $canInvoice = true;
                }
            }
        }
        //}

        if($request->getFullActionName() == 'sales_order_view'){
            if($canInvoice){
                $buttonList->add(
                    'invoice_button',
                    [
                        'label' => __('Invoice'),
                        'onclick' => 'setLocation(\'' . $this->getInvoiceUrl($request->getParam('order_id')) . '\')',
                        'class' => 'invoice'
                    ]
                );
            }
            if($canShip){
                $buttonList->add(
                    'shipment_button',
                    [
                        'label' => __('Ship'),
                        'onclick' => 'setLocation(\'' . $this->getShipUrl($request->getParam('order_id')) . '\')',
                        'class' => 'ship'
                    ]
                );
            }

        }

        return $buttonList;
    }

    public function getInvoiceUrl($orderId)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $urlManager = $objectManager->get('Magento\Backend\Model\UrlInterface');
        return $urlManager->getUrl('designnbuy_vendor/*/invoice', ['order_id' => $orderId]);
    }

    public function getShipUrl($orderId)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $urlManager = $objectManager->get('Magento\Backend\Model\UrlInterface');
        return $urlManager->getUrl('designnbuy_vendor/*/ship', ['order_id' => $orderId]);
    }
}