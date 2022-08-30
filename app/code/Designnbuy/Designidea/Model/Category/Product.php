<?php
namespace Designnbuy\Designidea\Model\Category;

class Product extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Product collection factory
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\Link\Product\CollectionFactory
     */
    protected $_productCollectionFactory;
    /**
     * designidea product collection factory.
     *
     * @var \Designnbuy\Designidea\Model\ResourceModel\Designidea\Product\CollectionFactory
     */
    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\ResourceModel\Product\Link\CollectionFactory $linkCollectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\Link\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\CatalogInventory\Helper\Stock $stockHelper
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    protected $_designIdeaProductCollectionFactory;

    protected $_designIdeaProductFactory;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\ResourceModel\Product\Link\CollectionFactory $linkCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\Link\Product\CollectionFactory $productCollectionFactory,
        \Magento\CatalogInventory\Helper\Stock $stockHelper,
        \Designnbuy\Designidea\Model\ResourceModel\Category\Product\CollectionFactory $designIdeaProductCollectionFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,

        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,

        array $data = []
    ) {
        $this->_linkCollectionFactory = $linkCollectionFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->stockHelper = $stockHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_designIdeaProductCollectionFactory = $designIdeaProductCollectionFactory;
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
        $this->_init('Designnbuy\Designidea\Model\ResourceModel\Category\Product');
    }

    /**
     * Save data for product Designidea-product relation
     * @access public
     * @param  Designnbuy\Designidea\Model\Designidea $designidea
     * @return Designnbuy\Designidea\Model\Designidea\Product
     * @author Ajay Makwana
     */
    public function saveProductDesignIdeaRelation($designidea)
    {
        $data = $designidea->getProductsData();
        if (!is_null($data)) {
            $this->_getResource()->saveProductDesignIdeaRelation($designidea, $data);
        }
        return $this;
    }

    /**
     * Retrieve linked product collection
     *
     * @return ProductCollection
     */
    public function getProductCollection($category)
    {
        $collection = $this->getCollection()->addDesignIdeaCategoryFilter($category);
        return $collection;
    }
}
