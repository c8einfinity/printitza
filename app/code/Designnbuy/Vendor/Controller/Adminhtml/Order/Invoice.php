<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Vendor\Controller\Adminhtml\Order;

use Magento\Backend\App\Action;
use Magento\Sales\Model\Order\Email\Sender\OrderCommentSender;

class Invoice extends \Magento\Backend\App\Action
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
        \Magento\Sales\Model\Order\ItemFactory $orderItemFactory
    ) {
        $this->_orderItemFactory = $orderItemFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Designnbuy_Vendor::order_invoice';

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
            try {
                //load by order
                $order = $this->_objectManager->create('Magento\Sales\Model\Order')
                    ->load($orderId);

                if (!$order->getId()) {
                    throw new \Magento\Framework\Exception\LocalizedException(__('The order no longer exists.'));
                }

                if (!$order->canInvoice()) {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __('The order does not allow an invoice to be created.')
                    );
                }


                // Initialize the order shipment object
                //$convertOrder = $this->_objectManager->create('Magento\Sales\Model\Convert\Order');
                $invoiceItems = [];
                // Loop through order items
                foreach ($order->getAllItems() AS $orderItem) {
                    $invoiceItems[$orderItem->getId()] = $orderItem->getQtyOrdered();
                }

                $invoice = $this->_objectManager->create('Magento\Sales\Model\Service\InvoiceService')->prepareInvoice($order, $invoiceItems);
                $invoice->register();
                $invoice->getOrder()->setIsInProcess(true);
                $amount = 100;
                $history = $invoice->getOrder()->addStatusHistoryComment(
                    'Partial amount of $' . $amount . ' captured automatically.', false
                );

                $history->setIsCustomerNotified(true);
                $current_user = 'Vendor1';
                $order->addStatusToHistory($order->getStatus(), 'Order Invoice Created By Vendor ' . $current_user, false);


                $transactionSave = $this->_objectManager->create(
                    \Magento\Framework\DB\Transaction::class
                )->addObject(
                    $invoice
                )->addObject(
                    $invoice->getOrder()
                );
                $invoice->save();
                $this->messageManager->addSuccess(__('The invoice has been created.'));
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
