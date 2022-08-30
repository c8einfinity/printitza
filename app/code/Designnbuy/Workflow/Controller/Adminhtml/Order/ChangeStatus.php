<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Workflow\Controller\Adminhtml\Order;

use Magento\Backend\App\Action;
use Designnbuy\Workflow\Model\Order\Email\Sender\WorkflowStatusSender;
use Magento\Sales\Api\OrderRepositoryInterface;

class ChangeStatus extends \Magento\Backend\App\Action
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
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;
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
        OrderRepositoryInterface $orderRepository,
        \Designnbuy\Workflow\Model\StatusFactory  $statusFactory
    ) {
        $this->_orderItemFactory = $orderItemFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->orderRepository = $orderRepository;
        $this->statusFactory = $statusFactory;
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
                $statusIdRequestParam = 'item_status_'.$itemId;
                $statusId = $data[$statusIdRequestParam];
                if (empty($statusId)) {
                    throw new \Magento\Framework\Exception\LocalizedException(__('Please select a status.'));
                }
                $status = $this->statusFactory->create()->load($statusId);
                $emailSubject = '';
                $emailBody = '';
                if($status && $status->getStatusId()){
                    $emailSubject = $status->getEmailSubject();
                    $emailBody = $status->getEmailBody();
                }

                $orderItem = $this->_orderItemFactory->create()->load($itemId);
                $orderItem->setWorkflowStatus($statusId);
                $orderItem->save();
                $orderId = $orderItem->getOrderId();
                $order = $this->_initOrder($orderId);
                $order->setEmailSubject($emailSubject);
                $order->setEmailBody($emailBody);

                /** @var WorkflowStatusSender $workflowStatusSender */
                $workflowStatusSender = $this->_objectManager
                    ->create(\Designnbuy\Workflow\Model\Order\Email\Sender\WorkflowStatusSender::class);

                $notify = true;
                $comment = __('Status has been updated.');
                $workflowStatusSender->send($order, $notify, $comment);
                $response = ['error' => true, 'message' => 'Status has been updated.'];

            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $response = ['error' => true, 'message' => $e->getMessage()];
            } catch (\Exception $e) {
                $response = ['error' => true, 'message' => __('We cannot set item status.')];
            }
            if (is_array($response)) {
                $resultJson = $this->resultJsonFactory->create();
                $resultJson->setData($response);
                return $resultJson;
            }
        }
        return $this->resultRedirectFactory->create()->setPath('sales/*/');
    }

    /**
     * Initialize order model instance
     *
     * @return \Magento\Sales\Api\Data\OrderInterface|false
     */
    protected function _initOrder($orderId)
    {
        $id = $orderId;
        try {
            $order = $this->orderRepository->get($id);
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addError(__('This order no longer exists.'));
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
            return false;
        } catch (InputException $e) {
            $this->messageManager->addError(__('This order no longer exists.'));
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        return $order;
    }

    /**
     * Check is allowed access
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }
}
