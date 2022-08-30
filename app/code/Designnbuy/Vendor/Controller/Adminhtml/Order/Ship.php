<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Vendor\Controller\Adminhtml\Order;

use Magento\Backend\App\Action;
use Magento\Sales\Model\Order\Email\Sender\OrderCommentSender;

class Ship extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Sales\Model\Order\ItemFactory
     */
    protected $_orderItemFactory;
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Sales\Model\Order\ItemFactory $orderItemFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Sales\Model\Order\ItemFactory $orderItemFactory,
        \Designnbuy\Vendor\Helper\Data $vendorData
    ) {
        $this->_orderItemFactory = $orderItemFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->vendorData = $vendorData;
        parent::__construct($context);
    }

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Designnbuy_Vendor::order_ship';

    /**
     * Add order comment action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams();
        $orderId = $data['order_id'];
        if ($data) {
            //load by order
            $order = $this->_objectManager->create('Magento\Sales\Model\Order')
                ->load($orderId);

            // Check if order can be shipped or has already shipped
            if (!$order->canShip()) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('You can\'t create an shipment.')
                );
            }

            // Initialize the order shipment object
            $convertOrder = $this->_objectManager->create('Magento\Sales\Model\Convert\Order');
            $shipment = $convertOrder->toShipment($order);
            $vendorCommission = 0;
            $addCommission = false;
            // Loop through order items
            foreach ($order->getAllItems() AS $orderItem) {
                // Check if order item has qty to ship or is virtual
                if (! $orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
                    continue;
                }
                $addCommission = true;
                $qtyShipped = $orderItem->getQtyToShip();

                // Create shipment item with qty
                $shipmentItem = $convertOrder->itemToShipmentItem($orderItem)->setQty($qtyShipped);

                // Add shipment item to shipment
                $shipment->addItem($shipmentItem);

                $vendorCommission += $orderItem->getVendorCommission();
            }

            // Register shipment
            $shipment->register();

            $shipment->getOrder()->setIsInProcess(true);
            $transactionSave = $this->_objectManager->create(
                \Magento\Framework\DB\Transaction::class
            )->addObject(
                $shipment
            )->addObject(
                $shipment->getOrder()
            );
            try {
                // Save created shipment and order
                $shipment->save();
                $shipment->getOrder()->save();

                $model = $this->_objectManager->create('Designnbuy\Vendor\Model\Transaction');
                $vendorUser = $this->vendorData->getVendorUser();

                $data= [];
                $data['vendor_id'] = $vendorUser->getId();
                $data['order_increment_id'] = $order->getIncrementId();
                $data['information'] = 'Order';
                $data['amount'] = $vendorCommission;
                $data['type'] = 'Credit';
                $model->setData($data);
                $model->save();

                // Send email
                $this->_objectManager->create('Magento\Shipping\Model\ShipmentNotifier')
                    ->notify($shipment);

                $shipment->save();
                $this->messageManager->addSuccess(__('The shipment has been created.'));
            } catch (\Exception $e) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __($e->getMessage())
                );
            }
        }
        return $this->resultRedirectFactory->create()->setPath('sales/order/view',['order_id' => $orderId]);
    }

    public function _isAllowed()
    {
        return true;
    }
}
