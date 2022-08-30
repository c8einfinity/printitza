<?php

namespace Unific\Connector\Plugin;

use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\ScopeInterface;
use Unific\Connector\Helper\Hmac;
use Unific\Connector\Helper\Message\Queue;
use Unific\Connector\Helper\Settings;

class AbstractPlugin
{
    /**
     * @var Queue
     */
    protected $queueHelper;
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
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
     * @var Emulation
     */
    protected $emulation;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param Hmac $hmacHelper
     * @param Queue $queueHelper
     * @param ProductMetadataInterface $productMetadata
     * @param ModuleListInterface $moduleList
     * @param Emulation $emulation
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Hmac $hmacHelper,
        Queue $queueHelper,
        ProductMetadataInterface $productMetadata,
        ModuleListInterface $moduleList,
        Emulation $emulation
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->queueHelper = $queueHelper;
        $this->hmacHelper = $hmacHelper;
        $this->productMetadata = $productMetadata;
        $this->moduleList = $moduleList;
        $this->emulation = $emulation;
    }

    /**
     * @param null $storeId
     * @return bool
     */
    public function isConnectorEnabled($storeId = null)
    {
        $confgValue = (int)$this->scopeConfig->getValue(
            'unific/connector/enabled',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return 1 === $confgValue;
    }

    /**
     * @param array $data
     * @param string $webhookEndpoint
     * @param int $priority
     * @param string $subject
     * @return string
     */
    public function processWebhook($data, $webhookEndpoint = '', $priority = 5, $subject = 'checkout/create')
    {
        if ($webhookEndpoint != '' && filter_var($webhookEndpoint, FILTER_VALIDATE_URL) !== false) {
            $headers = [];
            $headers['X-SUBJECT'] = $subject;

            return $this->queueHelper->queue(
                $webhookEndpoint,
                $data,
                $priority,
                $headers,
                \Zend\Http\Request::METHOD_POST,
                false,
                null,
                null,
                Settings::QUEUE_MAX_RETRIES
            );
        }
    }

    /**
     * @param int $storeId
     */
    protected function emulateStore(int $storeId)
    {
        $this->emulation->startEnvironmentEmulation($storeId, Area::AREA_FRONTEND, true);
    }

    /**
     *
     */
    protected function stopEmulation()
    {
        $this->emulation->stopEnvironmentEmulation();
    }
}
