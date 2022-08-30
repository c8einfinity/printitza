<?php

namespace Designnbuy\ConfigArea\Model\ConfigArea\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Framework\Data\OptionSourceInterface;

class ConfigArea extends AbstractSource implements OptionSourceInterface
{
    /**
     * @var \Magento\Framework\App\Cache\Type\Config
     */
    protected $_configCacheType;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Designidea factory
     *
     * @var \Designnbuy\ConfigArea\Model\ConfigAreaFactory
     */
    protected $_configAreaFactory;

    /**
     * Construct
     *
     * @param \Designnbuy\ConfigArea\Model\ConfigAreaFactory $_configAreaFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Cache\Type\Config $configCacheType
     */
    public function __construct(
        \Designnbuy\ConfigArea\Model\ConfigAreaFactory $_configAreaFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Cache\Type\Config $configCacheType
    ) {
        $this->_configAreaFactory = $_configAreaFactory;
        $this->_storeManager = $storeManager;
        $this->_configCacheType = $configCacheType;
    }

    /**
     * Get list of all available designideas
     *
     * @return array
     */
    public function getAllOptions()
    {
        $cacheKey = 'CONFIGAREAS_SELECT_STORE_' . $this->_storeManager->getStore()->getCode();
        if ($cache = $this->_configCacheType->load($cacheKey)) {
            $options = unserialize($cache);
        } else {
            $collection = $this->_configAreaFactory->create()->getResourceCollection()->addActiveFilter();
            $options = $collection->toOptionArray();
            $this->_configCacheType->save(serialize($options), $cacheKey);
        }
        return $options;
    }
}