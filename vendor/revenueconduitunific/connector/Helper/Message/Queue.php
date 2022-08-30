<?php

namespace Unific\Connector\Helper\Message;

use Magento\Framework\App\Filesystem\DirectoryList;
use Unific\Connector\Helper\Settings;

class Queue extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Unific\Connector\Helper\Guid
     */
    protected $guidHelper;

    /**
     * @var \Unific\Connector\Model\QueueFactory
     */
    protected $queueFactory;

    /**
     * @var \Unific\Connector\Api\QueueRepositoryInterface
     */
    protected $queueRepository;

    protected $cookieManager;

    protected $storeManager;

    /** @var \Magento\Framework\Filesystem\DirectoryList $dir **/
    protected $dir;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * Queue constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Unific\Connector\Helper\Guid $guidHelper
     * @param \Unific\Connector\Model\QueueFactory $queueFactory
     * @param \Unific\Connector\Api\QueueRepositoryInterface $queueRepository ,
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Filesystem\DirectoryList $dir
     * @param \Magento\Framework\Filesystem $filesystem
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Unific\Connector\Helper\Guid $guidHelper,
        \Unific\Connector\Model\QueueFactory $queueFactory,
        \Unific\Connector\Api\QueueRepositoryInterface $queueRepository,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Filesystem\DirectoryList $dir,
        \Magento\Framework\Filesystem $filesystem
    ) {
        parent::__construct($context);

        $this->guidHelper = $guidHelper;
        $this->queueFactory = $queueFactory;
        $this->queueRepository = $queueRepository;
        $this->cookieManager = $cookieManager;
        $this->storeManager = $storeManager;
        $this->dir = $dir;
        $this->filesystem = $filesystem;
    }

    /**
     * Queue a message for sending
     *
     * @param $url
     * @param array $data
     * @param int $priority
     * @param $extraHeaders
     * @param $requestType
     * @param bool $historical
     * @param int $responseHttpCode
     * @param int $retryAmount
     * @param int $maxRetryAmount
     * @param null $guid
     * @return
     */
    public function queue(
        $url,
        $data,
        $priority,
        $extraHeaders,
        $requestType = \Zend\Http\Request::METHOD_POST,
        $historical = false,
        $responseHttpCode = 200,
        $retryAmount = 0,
        $maxRetryAmount = Settings::QUEUE_MAX_RETRIES,
        $guid = null
    ) {
        $messageModel = $this->queueFactory->create();

        try {
            // Set the GUID, also in the headers
            $guid = ($guid == null) ? $this->guidHelper->generateGuid() : $guid;

            $data['metadata'] = [];
            $data['metadata']['website'] = $this->storeManager->getWebsite()->getCode();
            $data['metadata']['store'] = $this->storeManager->getGroup()->getCode();
            $data['metadata']['storeview'] = $this->storeManager->getStore()->getCode();

            if ($this->cookieManager->getCookie('hubspotutk') != null) {
                $data['metadata']['hubspotutk'] = $this->cookieManager->getCookie('hubspotutk');
            }

            // Sorting all data arrays alphabetically to keep it consistent
            ksort($data);

            $messageModel->setGuid($guid);
            $messageModel->setUrl($url);
            $messageModel->setHeaders(json_encode($extraHeaders));
            $messageModel->setMessage(json_encode($data));
            $messageModel->setRequestType($requestType);
            $messageModel->setResponseHttpCode($responseHttpCode);
            $messageModel->setRetryAmount($retryAmount);
            $messageModel->setMaxRetryAmount($maxRetryAmount);
            $messageModel->setHistorical($historical);
            $messageModel->setPriority($priority);

            if (!$historical || !$this->scopeConfig->getValue('unific/webhook/historical_save_to_file')) {
                $this->queueRepository->save($messageModel);
            } else {
                $header = $extraHeaders;
                $prefix = $header['X-SUBJECT'];
                $filename = $prefix . '.json';
                $unificDirectoryName = 'unific';

                $unificDirectory = $this->dir->getPath(DirectoryList::VAR_DIR) . DIRECTORY_SEPARATOR . $unificDirectoryName;

                $directoryWriter = $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
                $directoryWriter->writeFile(
                    $unificDirectory . DIRECTORY_SEPARATOR . $filename,
                    json_encode($messageModel->getData()) . ",\n",
                    'a'
                );
            }
        } catch (\Exception $e) {
            $this->_logger->debug($e->getMessage());
        }

        return $messageModel->getGuid();
    }
}
