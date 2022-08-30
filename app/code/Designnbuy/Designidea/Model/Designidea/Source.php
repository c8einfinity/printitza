<?php

namespace Designnbuy\Designidea\Model\Designidea;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Framework\Data\OptionSourceInterface;

class Source extends AbstractSource implements OptionSourceInterface
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
     * Designidea collection factory.
     *
     * @var \Designnbuy\Designidea\Model\ResourceModel\Category\CollectionFactory
     */
    protected $_designideaCollectionFactory;

    /**
     * Construct
     *
     * @param \Designnbuy\Designidea\Model\DesignideaFactory $designideaFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Cache\Type\Config $configCacheType
     */
    public function __construct(
        \Designnbuy\Designidea\Model\ResourceModel\Designidea\CollectionFactory $designideaCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Cache\Type\Config $configCacheType
    ) {
        $this->_designideaCollectionFactory = $designideaCollectionFactory;
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
            //$collection = $this->_categoryFactory->create()->getResourceCollection()->loadByStore();
            $collection = $this->_designideaCollectionFactory->create();
            $collection->addAttributeToSelect('*');
            $collection->joinAttribute('title','designnbuy_designidea/title','entity_id',null,'left',0);
            
            $options = $collection->toOptionArray();
            $this->_configCacheType->save(serialize($options), $cacheKey);
        }
        return $options;
    }
}