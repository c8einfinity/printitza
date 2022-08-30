<?php

namespace Unific\Connector\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Store\Model\ScopeInterface;
use Unific\Connector\Helper\Data;
use Unific\Connector\Helper\Hmac;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var Data
     */
    protected $unificHelper;

    /**
     * @var Hmac
     */
    protected $hmacHelper;

    /**
     * @var WriterInterface
     */
    protected $configWriter;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    private $secretKey = 'unific/hmac/hmacSecret';

    /**
     * Init
     *
     * @param Data $unificHelper
     * @param Hmac $hmacHelper
     * @param WriterInterface $configWriter
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Data $unificHelper,
        Hmac $hmacHelper,
        WriterInterface $configWriter,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->unificHelper = $unificHelper;
        $this->hmacHelper = $hmacHelper;
        $this->configWriter = $configWriter;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $this->unificHelper->createApiUser();

        if ($this->scopeConfig->getValue($this->secretKey, ScopeInterface::SCOPE_STORE) == null ||
            $this->scopeConfig->getValue($this->secretKey, ScopeInterface::SCOPE_STORE) == '') {
            $this->configWriter->save($this->secretKey, $this->hmacHelper->generateSecret());
        }

        $setup->endSetup();
    }
}
