<?php
namespace Designnbuy\Template\Model\Template;

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
        \Designnbuy\Template\Model\ResourceModel\Template\Upsell\CollectionFactory $upsellCollectionFactory,
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
        $this->_init('Designnbuy\Template\Model\ResourceModel\Template\Upsell');
    }

    /**
     * Save data for product Template-product relation
     * @access public
     * @param  Designnbuy\Template\Model\Template $template
     * @return Designnbuy\Template\Model\Template\Product
     * @author Ajay Makwana
     */
    public function saveUpsellTemplateRelation($template)
    {
        $data = $template->getUpsellData();
        $this->_getResource()->saveUpsellTemplateRelation($template, $data);
        return $this;
    }



    /**
     * Retrieve linked product collection
     *
     * @return ProductCollection
     */
    public function getUpsellCollection($template)
    {
        $collection = $this->_getResource()->getUpsellTemplates($template);
        
        return $collection;
    }
}
