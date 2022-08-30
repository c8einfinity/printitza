<?php

namespace Designnbuy\PrintingMethod\Model\PrintingMethod\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Framework\Data\OptionSourceInterface;

class PrintingMethod extends AbstractSource implements OptionSourceInterface
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
     * @var \Designnbuy\PrintingMethod\Model\PrintingMethodFactory
     */
    protected $_printingMethodFactory;

    /**
     * Construct
     *
     * @param \Designnbuy\Designidea\Model\DesignideaFactory $designideaFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Cache\Type\Config $configCacheType
     */
    public function __construct(
        \Designnbuy\PrintingMethod\Model\PrintingMethodFactory $_printingMethodFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Cache\Type\Config $configCacheType
    ) {
        $this->_printingMethodFactory = $_printingMethodFactory;
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
        $cacheKey = 'DESIGNIDEA_SELECT_STORE_' . $this->_storeManager->getStore()->getCode();
        if ($cache = $this->_configCacheType->load($cacheKey)) {
            $options = unserialize($cache);
        } else {
            $collection = $this->_printingMethodFactory->create()->getResourceCollection();
            $options = $collection->toOptionArray();
            $this->_configCacheType->save(serialize($options), $cacheKey);
        }
        return $options;
    }
}