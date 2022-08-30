<?php

namespace Designnbuy\JobManagement\Controller\Adminhtml\Jobmanagement;

use Magento\Sales\Api\OrderRepositoryInterface;
use Designnbuy\Workflow\Model\Order\Email\Sender\WorkflowStatusSender;
use Magento\Sales\Model\Order\Email\Sender\OrderCommentSender;

class Updatejob extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \\Designnbuy\JobManagement\Model\JobmanagementFactory
     */
    protected $jobManagementFactory;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Sales\Model\Order\ItemFactory $orderItemFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Designnbuy\JobManagement\Model\JobmanagementFactory $jobManagementFactory,
        \Magento\Sales\Model\Order\ItemFactory $orderItemFactory,
        OrderRepositoryInterface $orderRepository,
        \Designnbuy\Workflow\Model\StatusFactory  $statusFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_date = $date;
        $this->_jobManagementFactory = $jobManagementFactory;
        $this->_orderItemFactory = $orderItemFactory;
        $this->orderRepository = $orderRepository;
        $this->statusFactory = $statusFactory;
        parent::__construct($context);
    }

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
                $entityId = $data['entity_id'];
                $itemId = $data['item_id'];

                if (empty($itemId)) {
                    throw new \Magento\Framework\Exception\LocalizedException(__('Please select an Item.'));
                }

                ## WorkFlowStatus Update
                /*$statusIdRequestParam = 'item_status_'.$itemId;
                $statusId = $data[$statusIdRequestParam];*/
                $statusId = 49;

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

                //$workflowStatusSender = $this->_objectManager->create(\Designnbuy\Workflow\Model\Order\Email\Sender\WorkflowStatusSender::class);                    
                $notify = true;
                $comment = __('Status has been updated.');
                //$workflowStatusSender->send($order, $notify, $comment);

                //$response = ['error' => true, 'message' => 'Status has been updated.'];
                ## End Workflow Status

                $jobManagement = $this->_jobManagementFactory->create()->load($entityId);
                $startDate = $this->_getCurrentDate();

                $data['workflow_status_id'] = $statusId;
                $data['update_date'] = $startDate;
                $data['due'] = $startDate;
                $jobManagement->addData($data);
                $jobManagement->save();
                $entityId = $jobManagement->getEntityId();

            } catch (\Magento\Framework\Exception\LocalizedException $e) {

                $this->messageManager->addException(
                $e,
                    __(
                        'Something went wrong while saving record %1. %2',
                        strtolower($entityId),
                        $e->getMessage()
                    )
                );
                $this->_redirect('*/*/');
                //$response = ['error' => true, 'message' => $e->getMessage()];
            } 
            
        }
        $this->_redirect('*/*/');
        //return $this->resultRedirectFactory->create()->setPath('sales/*/');
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
     * Process Writer data before saving
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function _getCurrentDate()
    {
        $gmtDate = $this->_date->gmtDate();
        return $gmtDate;
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
