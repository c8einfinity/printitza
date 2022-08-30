<?php

namespace Unific\Connector\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Unific\Connector\Api\Data\QueueInterface;
use Unific\Connector\Connection\Connection;
use Unific\Connector\Model\Queue as QueueModel;
use Unific\Connector\Model\ResourceModel\Queue as QueueResource;
use Unific\Connector\Model\ResourceModel\Queue\Collection;
use Unific\Connector\Model\ResourceModel\Queue\CollectionFactory;
use Zend\Http\Request;

class Queue extends AbstractHelper
{
    /**
     * @var Logger
     */
    protected $logger;
    /**
     * @var Connection
     */
    protected $restConnection;
    /**
     * @var CollectionFactory
     */
    protected $queueCollectionFactory;
    /**
     * @var Hmac
     */
    protected $hmacHelper;
    /**
     * @var ProductMetadataInterface
     */
    protected $productMetadata;
    /**
     * @var ModuleListInterface
     */
    protected $moduleList;
    /**
     * @var QueueResource
     */
    protected $queueResource;
    /**
     * @var LoggerInterface
     */
    protected $fileLog;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Queue constructor.
     * @param Context $context
     * @param Logger $logger
     * @param Hmac $hmacHelper
     * @param CollectionFactory $queueCollectionFactory
     * @param Connection $restConnection
     * @param ProductMetadataInterface $productMetadata
     * @param ModuleListInterface $moduleList
     * @param QueueResource $queueResource
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        Logger $logger,
        Hmac $hmacHelper,
        CollectionFactory $queueCollectionFactory,
        Connection $restConnection,
        ProductMetadataInterface $productMetadata,
        ModuleListInterface $moduleList,
        QueueResource $queueResource,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);

        $this->logger = $logger;
        $this->restConnection = $restConnection;
        $this->queueCollectionFactory = $queueCollectionFactory;
        $this->hmacHelper = $hmacHelper;
        $this->productMetadata = $productMetadata;
        $this->moduleList = $moduleList;
        $this->fileLog = $context->getLogger();
        $this->queueResource = $queueResource;
        $this->storeManager = $storeManager;
    }

    /**
     * @return void
     * @throws \Exception
     */
    public function process()
    {
        if ($this->scopeConfig->getValue('unific/connector/integration', ScopeInterface::SCOPE_STORE) == "") {
            $this->fileLog->info(
                'Could not process the Unific queue because the integration id was not set.
                Please configure the extension'
            );
            return;
        }

        $this->sendDataFromQueue(
            false,
            $this->getWebhookConfigValue('message_task_limit', Settings::QUEUE_LIVE_PER_MINUTE)
        );
        $this->sendDataFromQueue(
            true,
            $this->getWebhookConfigValue('historical_task_limit', Settings::QUEUE_HISTORICAL_PER_MINUTE)
        );
    }

    /**
     * @param bool $isHistorical
     * @param int $size
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function sendDataFromQueue($isHistorical = false, $size = 100)
    {
        // For efficiency we do not loop everything constantly
        $headers = [];
        $headers['Content-Type'] = 'application/json';
        $headers['Accept'] = 'application/json';
        $headers['X-UNIFIC-INTEGRATION-ID']
            = $this->scopeConfig->getValue('unific/connector/integration', ScopeInterface::SCOPE_STORE);
        $headers['X-MAGENTO-VERSION'] = $this->productMetadata->getVersion();
        $headers['X-UNIFIC-CONNECTOR-VERSION'] = $this->moduleList->getOne('Unific_Connector')['setup_version'];
        $headers['X-MAGENTO-DOMAIN'] = $this->storeManager->getStore()
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);

        $pageSize = $this->getPageSize($isHistorical);
        if ($size < $pageSize) {
            $size = $pageSize;
        }
        if ($size % $pageSize !== 0) {
            //adjust size to be multiplier of page size
            $size = ceil($size / $pageSize) * $pageSize;
        }

        $iteration = 0;
        while ($iteration < $size) {
            $collection = $queueItem = $this->getNextItems($isHistorical);
            if ($collection->count() === 0) {
                $iteration = $size;
            }
            foreach ($collection as $queueItem) {
                /** @var QueueInterface $queueItem */
                try {
                    if ($queueItem->getGuid()) {
                        $this->sendMessage($headers, $queueItem);
                        $iteration++;
                    }
                } catch (\Exception $e) {
                    $iteration++;
                }
            }
        }
    }

    /**
     * @param array $headers
     * @param QueueModel $queueItem
     * @throws \Exception
     */
    protected function sendMessage($headers, QueueModel $queueItem)
    {
        $queueData = json_decode($queueItem->getMessage(), true);

        if (!is_array($queueData)) {
            // Increment the retry amount
            $queueItem->setRetryAmount($queueItem->getRetryAmount() + 1);
            $queueItem->setStatusChange(null);
            $queueItem->setStatus(Settings::QUEUE_ITEM_STATUS_PENDING);
            $queueItem->save();

            $this->logger->createLogForCanceledWebhook(
                $queueItem,
                $queueItem->getGuid() . 'Cannot send message, body empty'
            );
            return;
        }

        if ($queueItem->getUrl() != '' && filter_var($queueItem->getUrl(), FILTER_VALIDATE_URL) !== false) {
            switch ($queueItem->getRequestType()) {
                case 'POST':
                    $type = Request::METHOD_POST;
                    break;
                case 'PUT':
                    $type = Request::METHOD_PUT;
                    break;
                case 'DELETE':
                    $type = Request::METHOD_DELETE;
                    break;
                default:
                    $type = Request::METHOD_GET;
                    break;
            }

            $headers = array_merge($headers, json_decode($queueItem->getHeaders(), true));
            $headers['X-MAGENTO-UNIFIC-HMAC'] = $this->hmacHelper->generateHmac($queueData);
            $headers['X-MAGENTO-GUID'] = $queueItem->getGuid();

            $response = $this->restConnection->sendData($queueItem->getUrl(), $queueData, $headers, $type);

            if ($response->isSuccess()) {
                $this->logger->createLog(
                    $queueItem,
                    $response,
                    'Webhook sent succesfully and removed from queue'
                );
                $queueItem->delete();
            } else {
                $queueItem->setRetryAmount($queueItem->getRetryAmount() + 1);
                $queueItem->setResponseHttpCode($response->getStatusCode());
                $queueItem->setStatusChange(null);
                $queueItem->setStatus(Settings::QUEUE_ITEM_STATUS_PENDING);
                $queueItem->save();

                if ($queueItem->getRetryAmount() == $queueItem->getMaxRetryAmount()) {
                    $this->logger->createLog(
                        $queueItem,
                        $response,
                        'Webhook reached maximum retry amount. Sending is stopped until a retry is forced.'
                    );
                } else {
                    $retryAttempt = $queueItem->getRetryAmount() . '/' . $queueItem->getMaxRetryAmount();
                    $this->logger->createLog(
                        $queueItem,
                        $response,
                        'Webhook failed, queueing retry attempt ' . $retryAttempt
                    );
                }
            }
        } else {
            $this->logger->createLogForCanceledWebhook(
                $queueItem,
                'Message not send and removed from queue, no valid endpoint configured'
            );
            $queueItem->delete();
        }
    }

    /**
     * @param bool $isHistorical
     * @return iterable
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getNextItems(bool $isHistorical)
    {
        /** @var Collection $collection */
        $collection = $this->queueCollectionFactory->create();
        $collection->addFieldToFilter(
            'historical',
            ['eq' => (int)$isHistorical]
        );
        $collection->addFieldToFilter(
            'retry_amount',
            ['lt' => $isHistorical ? Settings::QUEUE_HISTORICAL_MAX_RETRIES : Settings::QUEUE_MAX_RETRIES]
        );

        $collection->setPageSize($this->getPageSize($isHistorical));
        $collection->setCurPage(1);

        // select pending messages or processing not updated for the last 15 minutes
        $collection->getSelect()
            ->where(
                new \Zend_Db_Expr(
                    $collection->getConnection()->quoteInto('status = ?', Settings::QUEUE_ITEM_STATUS_PENDING)
                    . ' OR '
                    . '(' .
                    $collection->getConnection()->quoteInto('status = ?', Settings::QUEUE_ITEM_STATUS_PROCESSING)
                    . ' AND ' .
                    $collection->getConnection()->quoteInto('status_change < ?', (time() - (15 * 60)))
                    . ')'
                )
            );
        $collection->getSelect()->order(['retry_amount ASC', 'priority ASC']);

        // lock items
        $ids = array_map(function ($item) {
            return $item->getGuid();
        }, $collection->getItems());
        $this->queueResource->getConnection()->update(
            $this->queueResource->getMainTable(),
            ['status' => Settings::QUEUE_ITEM_STATUS_PROCESSING, 'status_change' => time()],
            ['guid in (?)' => $ids]
        );

        return $collection;
    }

    /**
     * @param $isHistorical
     * @return int
     */
    protected function getPageSize($isHistorical)
    {
        return $isHistorical
            ? $this->getWebhookConfigValue('historical_batch_size', Settings::HISTORICAL_PAGE_SIZE)
            : $this->getWebhookConfigValue('message_batch_size', Settings::LIVE_PAGE_SIZE);
    }

    protected function getWebhookConfigValue($field, $default)
    {
        return $this->scopeConfig->getValue('unific/webhook/' . $field) ?: $default;
    }
}
