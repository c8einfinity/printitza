<?php

namespace Designnbuy\Commission\Model;

/**
 * Redemption url model
 */
class Url
{
    /**
     * Permalink Types
     */
    /**
     * Objects Types
     */
    const CONTROLLER_REDEMPTION = 'redemption';

    const CONTROLLER_COMMISSION = 'commission';
    
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Store id
     * @var int | null
     */
    protected $storeId;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\UrlInterface $url
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Framework\UrlInterface $url,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_registry = $registry;
        $this->_url = $url;
        $this->_storeManager = $storeManager;
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * Retrieve permalink type
     * @return string
     */

    public function getPermalinkType()
    {
        return $this->_getConfig('type');
    }

    /**
     * Retrieve route name by controller
     * @param  string  $controllerName
     * @param  boolean $skip
     * @return string || null
     */
    public function getRoute($controllerName = null, $skip = true)
    {
        if ($controllerName) {
            $controllerName .= '_';
        }
        return 'redemption';
    }

    /**
     * Retrieve controller name by route
     * @param  string  $route
     * @param  boolean $skip
     * @return string || null
     */
    public function getControllerName($route, $skip = true)
    {
        foreach ([
            self::CONTROLLER_REDEMPTION            
        ] as $controllerName) {
            if ($this->getRoute($controllerName) == $route) {
                return $controllerName;
            }
        }

        return $skip ? $route : null;
    }

    /**
     * Retrieve redemption base url
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->_url->getUrl($this->getRoute());
    }

    /**
     * Retrieve redemption page url
     * @param  string $identifier
     * @param  string $controllerName
     * @return string
     */
    public function getUrl($identifier, $controllerName)
    {
        $url = $this->_url->getUrl('', [
            '_direct' => $this->getUrlPath($identifier, $controllerName)
        ]);
        return $url;
    }
}
