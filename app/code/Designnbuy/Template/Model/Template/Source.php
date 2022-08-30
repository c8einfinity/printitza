<?php

namespace Designnbuy\Template\Model\Template;

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
     * Template collection factory.
     *
     * @var \Designnbuy\Template\Model\ResourceModel\Category\CollectionFactory
     */
    protected $_templateCollectionFactory;

    /**
     * Construct
     *
     * @param \Designnbuy\Template\Model\TemplateFactory $templateFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Cache\Type\Config $configCacheType
     */
    public function __construct(
        \Designnbuy\Template\Model\ResourceModel\Template\CollectionFactory $templateCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Cache\Type\Config $configCacheType
    ) {
        $this->_templateCollectionFactory = $templateCollectionFactory;
        $this->_storeManager = $storeManager;
        $this->_configCacheType = $configCacheType;
    }

    /**
     * Get list of all available templates
     *
     * @return array
     */
    public function getAllOptions()
    {
        $cacheKey = 'TEMPLATE_SELECT_STORE_' . $this->_storeManager->getStore()->getCode();
        if ($cache = $this->_configCacheType->load($cacheKey)) {
            $options = unserialize($cache);
        } else {
            //$collection = $this->_categoryFactory->create()->getResourceCollection()->loadByStore();
            $collection = $this->_templateCollectionFactory->create();
            $collection->addAttributeToSelect('*');
            $collection->joinAttribute('title','designnbuy_template/title','entity_id',null,'left',0);
            
            $options = $collection->toOptionArray();
            $this->_configCacheType->save(serialize($options), $cacheKey);
        }
        
        $doubleCategoryTemplate = array();
        foreach($options as $key => $option){
            if(isset($option['category_id'])){
                $optionCategory = explode(',', $option['category_id']);
                if(count($optionCategory) > 1){
                    foreach($optionCategory as $optCategory){
                        $doubleCategoryTemplate[] = array("label" => $option['label'], "value" => $option['value'], "category_id" => $optCategory);
                    }
                unset($options[$key]);
                }
            }
        }
        $optionTemplateDt = array_merge($options,$doubleCategoryTemplate);
        //echo "<pre>"; print_r($optionTemplateDt); exit;
        //echo "<pre>"; print_r($options);
        //echo "<pre>"; print_r($doubleCategoryTemplate); exit;
        
        return $optionTemplateDt;
    }
}