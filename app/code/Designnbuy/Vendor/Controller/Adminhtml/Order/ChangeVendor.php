<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Vendor\Controller\Adminhtml\Order;

use Magento\Backend\App\Action;
use Magento\Sales\Model\Order\Email\Sender\OrderCommentSender;

class ChangeVendor extends \Magento\Backend\App\Action
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

    protected $vendorHelper;
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
        \Designnbuy\Vendor\Helper\Data $vendorHelper
    ) {
        $this->_orderItemFactory = $orderItemFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->vendorHelper = $vendorHelper;
        parent::__construct($context);
    }

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Sales::comment';

    /**
     * Add order comment action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams();

        if ($data) {
            try {

                $itemId = $data['item_id'];
                $orderId = $data['order_id'];
                $vendorIdRequestParam = 'item_vendor_'.$itemId;
                $vendorId = $data[$vendorIdRequestParam];
                if (empty($vendorId)) {
                    throw new \Magento\Framework\Exception\LocalizedException(__('Please select a vendor.'));
                }
                $orderItem = $this->_orderItemFactory->create()->load($itemId);

                $orderItem->setVendorId($vendorId);
                $orderItem->save();

                $vendorCommission = $this->vendorHelper->getVendorCommission($orderItem);
                $orderItem->setVendorCommission($vendorCommission);
                $orderItem->save();

                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                
                if($orderItem->getVendorId()){
                    
                    $order = $objectManager->create('Magento\Sales\Model\Order')->load($orderId);
                    $vendor = $objectManager->create('Magento\User\Model\User')->load($orderItem->getVendorId());
                    if(!empty($vendor)){
                        $order->setCustomerEmail($vendor->getEmail());
                        $objectManager->create('\Magento\Sales\Model\OrderNotifier')
                        ->notify($order);
                    }
                }


                ## Job Assignment to Vendor @13
                $this->_eventManager->dispatch('designnbuy_jobmanagement_vendor_assignment', ['item_id' => $itemId, 'vendor_user_id' => $vendorId]);
                ##End @13
                
                $orderUrl = $this->getUrl('sales/order/view', ['order_id' => $orderId]);
                $response = ['ajaxRedirect' => $orderUrl,'ajaxExpired' => true, 'error' => true, 'message' => 'Vendor has been updated.', 'commission' => $vendorCommission];

            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $response = ['error' => true, 'message' => $e->getMessage()];
            } catch (\Exception $e) {
                $response = ['error' => true, 'message' => __('We cannot set item vendor.')];
            }
            if (is_array($response)) {
                $resultJson = $this->resultJsonFactory->create();
                $resultJson->setData($response);
                return $resultJson;
            }
        }
        return $this->resultRedirectFactory->create()->setPath('sales/*/');
    }
}
