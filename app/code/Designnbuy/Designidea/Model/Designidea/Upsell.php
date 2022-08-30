<?php
namespace Designnbuy\Designidea\Model\Designidea;

class Upsell extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Product collection factory
     *
     * @var \Magento\Catalog\Model\ResourceModel\Upsell\CollectionFactory
     */
    
    protected $_upsellCollectionFactory;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\ResourceModel\Product\Link\CollectionFactory $linkCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\Link\Product\CollectionFactory $productCollectionFactory,
        \Magento\CatalogInventory\Helper\Stock $stockHelper,
        \Designnbuy\Designidea\Model\ResourceModel\Designidea\Upsell\CollectionFactory $upsellCollectionFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_linkCollectionFactory = $linkCollectionFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->stockHelper = $stockHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_upsellCollectionFactory = $upsellCollectionFactory;
    }


    /**
     * Initialize resource
     *
     * @access protected
     * @return void
     * @author Ajay Makwana
     */
    protected function _construct()
    {
        $this->_init('Designnbuy\Designidea\Model\ResourceModel\Designidea\Upsell');
    }

    /**
     * Save data for product Designidea-product relation
     * @access public
     * @param  Designnbuy\Designidea\Model\Designidea $designidea
     * @return Designnbuy\Designidea\Model\Designidea\Product
     * @author Ajay Makwana
     */
    public function saveUpsellDesignideaRelation($designidea)
    {
        $data = $designidea->getUpsellData();
        $this->_getResource()->saveUpsellDesignideaRelation($designidea, $data);
        return $this;
    }



    /**
     * Retrieve linked product collection
     *
     * @return ProductCollection
     */
    public function getUpsellCollection($designidea)
    {
        $collection = $this->_getResource()->getUpsellDesignideas($designidea);
        
        return $collection;
    }
}
