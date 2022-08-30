<?php
namespace Designnbuy\Designidea\Model\Designidea;

class Crosssell extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Product collection factory
     *
     * @var \Magento\Catalog\Model\ResourceModel\Crosssell\CollectionFactory
     */
    
    protected $_crosssellCollectionFactory;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\ResourceModel\Product\Link\CollectionFactory $linkCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\Link\Product\CollectionFactory $productCollectionFactory,
        \Magento\CatalogInventory\Helper\Stock $stockHelper,
        \Designnbuy\Designidea\Model\ResourceModel\Designidea\Crosssell\CollectionFactory $crosssellCollectionFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_linkCollectionFactory = $linkCollectionFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->stockHelper = $stockHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_crosssellCollectionFactory = $crosssellCollectionFactory;
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
        $this->_init('Designnbuy\Designidea\Model\ResourceModel\Designidea\Crosssell');
    }

    /**
     * Save data for product Designidea-product relation
     * @access public
     * @param  Designnbuy\Designidea\Model\Designidea $designidea
     * @return Designnbuy\Designidea\Model\Designidea\Product
     * @author Ajay Makwana
     */
    public function saveCrosssellDesignideaRelation($designidea)
    {
        $data = $designidea->getCrosssellData();
        $this->_getResource()->saveCrosssellDesignideaRelation($designidea, $data);
        return $this;
    }



    /**
     * Retrieve linked product collection
     *
     * @return ProductCollection
     */
    public function getCrosssellCollection($designidea)
    {
        $collection = $this->_getResource()->getCrosssellDesignideas($designidea);
        
        return $collection;
    }
}
