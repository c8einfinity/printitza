<?php

namespace Designnbuy\JobManagement\Controller\Adminhtml\Order;

use Magento\Sales\Api\OrderRepositoryInterface;

class Jobstart extends \Magento\Backend\App\Action
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
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Sales\Model\Order\ItemFactory $orderItemFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Designnbuy\JobManagement\Model\JobmanagementFactory $jobManagementFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_date = $date;
        $this->_jobManagementFactory = $jobManagementFactory;
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
                $itemId = $data['item_id'];
                if (empty($itemId)) {
                    throw new \Magento\Framework\Exception\LocalizedException(__('Please select an Item.'));
                }

                $jobManagement = $this->_jobManagementFactory->create();
                $startDate = $this->_getCurrentDate();
                $data['created_date'] = $startDate;
                $jobManagement->addData($data);
                $jobManagement->save();
                $this->messageManager->addSuccess(__('Job process has been started.'));

            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addException(
                    $e,__('Something went wrong while saving job', $e->getMessage())
                );
            } catch (\Exception $e) {
                $this->messageManager->addException(
                    $e,__('Something went wrong while saving job', $e->getMessage())
                );
            }
            /*if (is_array($response)) {
                $resultJson = $this->resultJsonFactory->create();
                $resultJson->setData($response);
                return $resultJson;
            }*/
        }
        $this->_redirect($this->_redirect->getRefererUrl());
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
