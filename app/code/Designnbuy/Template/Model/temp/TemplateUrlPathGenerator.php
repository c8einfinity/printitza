<?php

namespace Designnbuy\Template\Model;

use Magento\Store\Model\Store;

class TemplateUrlPathGenerator
{
    const XML_PATH_DEIGNIDEA_URL_SUFFIX = 'template/seo/template_url_suffix';

    /**
     * Cache for template rewrite suffix
     *
     * @var array
     */
    protected $templateUrlSuffix = [];

    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $storeManager;

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface */
    protected $scopeConfig;

    /** @var \Designnbuy\Template\Api\TemplateRepositoryInterface */
    protected $templateRepository;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Designnbuy\Template\Api\TemplateRepositoryInterface $templateRepository
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Designnbuy\Template\Api\TemplateRepositoryInterface $templateRepository
    ) {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->templateRepository = $templateRepository;
    }

    /**
     * Retrieve Template Url path
     *
     * @param \Designnbuy\Template\Model\Template $template
     *
     * @return string
     */
    public function getUrlPath($template)
    {
        $path = $template->getData('url_path');
        if ($path === null) {
            $path = $template->getUrlKey() === false
                ? $this->prepareTemplateDefaultUrlKey($template)
                : $this->prepareTemplateUrlKey($template);
        }
        return $path;
    }

    /**
     * Prepare URL Key with stored template data (fallback for "Use Default Value" logic)
     *
     * @param \Designnbuy\Template\Model\Template $template
     * @return string
     */
    protected function prepareTemplateDefaultUrlKey(\Designnbuy\Template\Model\Template $template)
    {
        $storedTemplate = $this->templateRepository->get($template->getId());
        $storedUrlKey = $storedTemplate->getUrlKey();
        return $storedUrlKey ?: $template->formatUrlKey($storedTemplate->getName());
    }

    /**
     * Retrieve Template Url path with suffix
     *
     * @param \Designnbuy\Template\Model\Template $template
     * @param int $storeId
     * @return string
     */
    public function getUrlPathWithSuffix($template, $storeId)
    {
        return $this->getUrlPath($template) . $this->getTemplateUrlSuffix($storeId);
    }

    /**
     * Get canonical template url path
     *
     * @param \Designnbuy\Template\Model\Template $template
     * @return string
     */
    public function getCanonicalUrlPath($template)
    {
        $path = 'template/template/view/id/' . $template->getId();
        return $path;
    }

    /**
     * Generate template url key based on url_key entered by merchant or template name
     *
     * @param \Designnbuy\Template\Model\Template $template
     * @return string
     */
    public function getUrlKey($template)
    {
        return $template->getUrlKey() === false ? false : $this->prepareTemplateUrlKey($template);
    }

    /**
     * Prepare url key for template
     *
     * @param \Designnbuy\Template\Model\Template $template
     * @return string
     */
    protected function prepareTemplateUrlKey(\Designnbuy\Template\Model\Template $template)
    {
        $urlKey = $template->getUrlKey();
        return $template->formatUrlKey($urlKey === '' || $urlKey === null ? $template->getName() : $urlKey);
    }

    /**
     * Retrieve template rewrite suffix for store
     *
     * @param int $storeId
     * @return string
     */
    protected function getTemplateUrlSuffix($storeId = null)
    {
        if ($storeId === null) {
            $storeId = $this->storeManager->getStore()->getId();
        }

        if (!isset($this->templateUrlSuffix[$storeId])) {
            $this->templateUrlSuffix[$storeId] = $this->scopeConfig->getValue(
                self::XML_PATH_DEIGNIDEA_URL_SUFFIX,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            );
        }
        return $this->templateUrlSuffix[$storeId];
    }
}
