<?php

namespace Unific\Connector\Plugin;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Store\Model\App\Emulation;
use Unific\Connector\Helper\Data\Product;
use Unific\Connector\Helper\Hmac;
use Unific\Connector\Helper\Message\Queue;
use Unific\Connector\Helper\Settings;

class ProductPlugin extends AbstractPlugin
{
    /**
     * @var Product
     */
    protected $productDataHelper;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param Hmac $hmacHelper
     * @param Queue $queueHelper
     * @param ProductMetadataInterface $productMetadata
     * @param ModuleListInterface $moduleList
     * @param Product $productDataHelper
     * @param Emulation $emulation
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Hmac $hmacHelper,
        Queue $queueHelper,
        ProductMetadataInterface $productMetadata,
        ModuleListInterface $moduleList,
        Product $productDataHelper,
        Emulation $emulation
    ) {
        parent::__construct(
            $scopeConfig,
            $hmacHelper,
            $queueHelper,
            $productMetadata,
            $moduleList,
            $emulation
        );

        $this->productDataHelper = $productDataHelper;
    }

    /**
     * @param \Magento\Catalog\Model\Product $subject
     * @return \Magento\Catalog\Model\Product
     */
    public function afterSave(\Magento\Catalog\Model\Product $subject)
    {
        if ($this->scopeConfig->getValue('unific/connector/enabled') == 1) {
            $integrationSubject = 'product/create';

            if ($subject->getCreatedAt() != $subject->getUpdatedAt() && $subject->getUpdatedAt() != null) {
                $integrationSubject = 'product/update';
            }

            $this->productDataHelper->setProduct($subject);
            $this->processWebhook(
                $this->productDataHelper->getProductInfo(),
                $this->scopeConfig->getValue('unific/webhook/product_endpoint'),
                Settings::PRIORITY_PRODUCT,
                $integrationSubject
            );
        }

        return $subject;
    }
}
