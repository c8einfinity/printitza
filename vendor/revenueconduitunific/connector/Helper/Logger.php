<?php
namespace Unific\Connector\Helper;

use Magento\Store\Model\ScopeInterface;
use Unific\Connector\Model\Audit\Log;

class Logger extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Unific\Connector\Model\Audit\LogFactory
     */
    protected $logFactory;

    protected $logCollectionFactory;

    /**
     * Logger constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Unific\Connector\Model\ResourceModel\Audit\Log\CollectionFactory $logCollectionFactory
     * @param \Unific\Connector\Model\Audit\LogFactory $logFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Unific\Connector\Model\ResourceModel\Audit\Log\CollectionFactory $logCollectionFactory,
        \Unific\Connector\Model\Audit\LogFactory $logFactory
    ) {

        parent::__construct($context);

        $this->logFactory = $logFactory;
        $this->logCollectionFactory = $logCollectionFactory;
    }

    /**
     * Write a log to the database
     *
     * @param $queueItem
     * @param $response
     * @param string $messageStatus
     * @throws \Exception
     */
    public function createLog($queueItem, $response, $messageStatus = '')
    {
        if ($this->scopeConfig->getValue('unific/connector/log', ScopeInterface::SCOPE_STORE) == 1) {
            if ($this->scopeConfig->getValue('unific/connector/log_severity') != 'all'
                && $response->isSuccess() === true
            ) {
                return;
            }

            $logModel = $this->createLogModel($queueItem);

            // The response
            $logModel->setResponseHttpCode($response->getStatusCode());
            $logModel->setResponseHeaders(json_encode($response->getHeaders()->toArray()));
            $logModel->setResponseMessage($response->getContent());

            // The status of the log
            $logModel->setMessageStatus($messageStatus);

            $logModel->save();
        }
    }

    /**
     * Write a log to the database
     *
     * @param $queueItem
     * @param string $messageStatus
     * @throws \Exception
     */
    public function createLogForCanceledWebhook($queueItem, $messageStatus = '')
    {
        if ($this->scopeConfig->getValue('unific/connector/log', ScopeInterface::SCOPE_STORE) == 1) {
            return;
        }
        $logModel = $this->createLogModel($queueItem);

        // The response
        $logModel->setResponseHttpCode(0);
        $logModel->setResponseHeaders('{}');
        $logModel->setResponseMessage('Message not send and removed from queue, no valid endpoint configured');

        // The status of the log
        $logModel->setMessageStatus($messageStatus);

        $logModel->save();
    }

    /**
     * @param $queueItem
     * @return Log
     */
    protected function createLogModel($queueItem)
    {
        /** @var Log $logModel */
        $logModel = $this->logFactory->create();

        // The request
        $logModel->setRequestGuid($queueItem->getGuid());
        $logModel->setRequestUrl($queueItem->getUrl());
        $logModel->setRequestHeaders($queueItem->getHeaders());
        $logModel->setRequestMessage($queueItem->getMessage());
        $logModel->setRequestType($queueItem->getRequestType());
        $logModel->setRetryAmount($queueItem->getRetryAmount());
        $logModel->setHistorical($queueItem->getHistorical());
        $logModel->setPriority($queueItem->getPriority());

        return $logModel;
    }

    /**
     * Clean the logfiles
     */
    public function cleanLog()
    {
        $now = new \DateTime();

        $collection = $this->logCollectionFactory->create();
        $collection->addFieldToFilter('date_created', ['lteq' => $now->format('Y-m-d H:i:s')]);
        $collection->walk('delete');
    }
}
