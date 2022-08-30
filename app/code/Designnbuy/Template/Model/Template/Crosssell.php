<?php
namespace Designnbuy\Template\Model\Template;

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
        \Designnbuy\Template\Model\ResourceModel\Template\Crosssell\CollectionFactory $crosssellCollectionFactory,
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
        $this->_init('Designnbuy\Template\Model\ResourceModel\Template\Crosssell');
    }

    /**
     * Save data for product Template-product relation
     * @access public
     * @param  Designnbuy\Template\Model\Template $template
     * @return Designnbuy\Template\Model\Template\Product
     * @author Ajay Makwana
     */
    public function saveCrosssellTemplateRelation($template)
    {
        $data = $template->getCrosssellData();
        $this->_getResource()->saveCrosssellTemplateRelation($template, $data);
        return $this;
    }



    /**
     * Retrieve linked product collection
     *
     * @return ProductCollection
     */
    public function getCrosssellCollection($template)
    {
        $collection = $this->_getResource()->getCrosssellTemplates($template);
        
        return $collection;
    }
}
