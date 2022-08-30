<?php

namespace Unific\Connector\Model\Api;

use Unific\Connector\Api\HistoricalManagementInterface;

class HistoricalManagement implements HistoricalManagementInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    protected $historicalHelper;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Unific\Connector\Helper\Historical $historicalHelper
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Unific\Connector\Helper\Historical $historicalHelper
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->historicalHelper = $historicalHelper;
    }

    /**
     * @api
     *
     * @return array
     */
    public function triggerHistorical()
    {
        return $this->historicalHelper->triggerHistorical();
    }

    /**
     * @api
     *
     * @param string $type
     * @return array
     */
    public function triggerHistoricalForType($type)
    {
        return $this->historicalHelper->triggerHistoricalForType($type, true);
    }

    /**
     * @api
     *
     * @param string $type
     * @return array
     */
    public function stopHistoricalForType($type)
    {
        return $this->historicalHelper->resetHistoricalForType($type);
    }

    /**
     * @api
     *
     * @return array
     */
    public function stopHistorical()
    {
        return $this->historicalHelper->resetAllHistoricalData();
    }
}
