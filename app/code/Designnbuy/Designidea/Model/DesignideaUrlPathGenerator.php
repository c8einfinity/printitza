<?php

namespace Designnbuy\Designidea\Model;

use Magento\Store\Model\Store;

class DesignideaUrlPathGenerator
{
    const XML_PATH_DESIGNIDEA_URL_SUFFIX = 'designidea/seo/designidea_url_suffix';

    /**
     * Cache for designidea rewrite suffix
     *
     * @var array
     */
    protected $designideaUrlSuffix = [];

    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $storeManager;

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface */
    protected $scopeConfig;

    /** @var \Designnbuy\Designidea\Api\DesignideaRepositoryInterface */
    protected $designideaRepository;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Designnbuy\Designidea\Api\DesignideaRepositoryInterface $designideaRepository
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Designnbuy\Designidea\Api\DesignideaRepositoryInterface $designideaRepository
    ) {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->designideaRepository = $designideaRepository;
    }

    /**
     * Retrieve Designidea Url path
     *
     * @param \Designnbuy\Designidea\Model\Designidea $designidea
     *
     * @return string
     */
    public function getUrlPath($designidea)
    {
        $path = $designidea->getData('url_path');
        if ($path === null) {
            $path = $designidea->getUrlKey() === false
                ? $this->prepareDesignideaDefaultUrlKey($designidea)
                : $this->prepareDesignideaUrlKey($designidea);
        }
        return $path;
    }

    /**
     * Prepare URL Key with stored designidea data (fallback for "Use Default Value" logic)
     *
     * @param \Designnbuy\Designidea\Model\Designidea $designidea
     * @return string
     */
    protected function prepareDesignideaDefaultUrlKey(\Designnbuy\Designidea\Model\Designidea $designidea)
    {
        $storedDesignidea = $this->designideaRepository->get($designidea->getId());
        $storedUrlKey = $storedDesignidea->getUrlKey();
        return $storedUrlKey ?: $designidea->formatUrlKey($storedDesignidea->getName());
    }

    /**
     * Retrieve Designidea Url path with suffix
     *
     * @param \Designnbuy\Designidea\Model\Designidea $designidea
     * @param int $storeId
     * @return string
     */
    public function getUrlPathWithSuffix($designidea, $storeId)
    {
        return $this->getUrlPath($designidea) . $this->getDesignideaUrlSuffix($storeId);
    }

    /**
     * Get canonical designidea url path
     *
     * @param \Designnbuy\Designidea\Model\Designidea $designidea
     * @return string
     */
    public function getCanonicalUrlPath($designidea)
    {
        $path = 'designidea/designidea/view/id/' . $designidea->getId();
        return $path;
    }

    /**
     * Generate designidea url key based on url_key entered by merchant or designidea name
     *
     * @param \Designnbuy\Designidea\Model\Designidea $designidea
     * @return string
     */
    public function getUrlKey($designidea)
    {
        return $designidea->getUrlKey() === false ? false : $this->prepareDesignideaUrlKey($designidea);
    }

    /**
     * Prepare url key for designidea
     *
     * @param \Designnbuy\Designidea\Model\Designidea $designidea
     * @return string
     */
    protected function prepareDesignideaUrlKey(\Designnbuy\Designidea\Model\Designidea $designidea)
    {
        $urlKey = $designidea->getUrlKey();
        return $designidea->formatUrlKey($urlKey === '' || $urlKey === null ? $designidea->getName() : $urlKey);
    }

    /**
     * Retrieve designidea rewrite suffix for store
     *
     * @param int $storeId
     * @return string
     */
    protected function getDesignideaUrlSuffix($storeId = null)
    {
        if ($storeId === null) {
            $storeId = $this->storeManager->getStore()->getId();
        }

        if (!isset($this->designideaUrlSuffix[$storeId])) {
            $this->designideaUrlSuffix[$storeId] = $this->scopeConfig->getValue(
                self::XML_PATH_DESIGNIDEA_URL_SUFFIX,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            );
        }
        return $this->designideaUrlSuffix[$storeId];
    }
}
