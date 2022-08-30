<?php

namespace Unific\Connector\Plugin;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Store\Model\App\Emulation;
use Unific\Connector\Helper\Data\Category;
use Unific\Connector\Helper\Hmac;
use Unific\Connector\Helper\Message\Queue;
use Unific\Connector\Helper\Settings;

class CategoryPlugin extends AbstractPlugin
{
    /**
     * @var Category
     */
    protected $categoryDataHelper;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param Hmac $hmacHelper
     * @param Queue $queueHelper
     * @param ProductMetadataInterface $productMetadata
     * @param ModuleListInterface $moduleList
     * @param Category $categoryDataHelper
     * @param Emulation $emulation
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Hmac $hmacHelper,
        Queue $queueHelper,
        ProductMetadataInterface $productMetadata,
        ModuleListInterface $moduleList,
        Category $categoryDataHelper,
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

        $this->categoryDataHelper = $categoryDataHelper;
    }

    /**
     * @param \Magento\Catalog\Model\Category $subject
     * @return \Magento\Catalog\Model\Category
     */
    public function afterSave(\Magento\Catalog\Model\Category $subject)
    {
        if ($this->scopeConfig->getValue('unific/connector/enabled') == 1) {
            $integrationSubject = 'category/create';

            if ($subject->getCreatedAt() != $subject->getUpdatedAt() && $subject->getUpdatedAt() != null) {
                $integrationSubject = 'category/update';
            }

            $this->categoryDataHelper->setCategory($subject);
            $this->processWebhook(
                $this->categoryDataHelper->getCategoryInfo(),
                $this->scopeConfig->getValue('unific/webhook/category_endpoint'),
                Settings::PRIORITY_CATEGORY,
                $integrationSubject
            );
        }

        return $subject;
    }
}
