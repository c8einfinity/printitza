<?php
namespace Designnbuy\Template\Model\Template;

class Product extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Product collection factory
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\Link\Product\CollectionFactory
     */
    protected $_productCollectionFactory;
    /**
     * template product collection factory.
     *
     * @var \Designnbuy\Template\Model\ResourceModel\Template\Product\CollectionFactory
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
    protected $_templateProductCollectionFactory;

    protected $_templateProductFactory;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\ResourceModel\Product\Link\CollectionFactory $linkCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\Link\Product\CollectionFactory $productCollectionFactory,
        \Magento\CatalogInventory\Helper\Stock $stockHelper,
        \Designnbuy\Template\Model\ResourceModel\Template\Product\CollectionFactory $templateProductCollectionFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,

        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,

        array $data = []
    ) {
        $this->_linkCollectionFactory = $linkCollectionFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->stockHelper = $stockHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_templateProductCollectionFactory = $templateProductCollectionFactory;
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
        $this->_init('Designnbuy\Template\Model\ResourceModel\Template\Product');
    }

    /**
     * Save data for product Template-product relation
     * @access public
     * @param  Designnbuy\Template\Model\Template $template
     * @return Designnbuy\Template\Model\Template\Product
     * @author Ajay Makwana
     */
    public function saveProductTemplateRelation($template)
    {
        $data = $template->getProductsData();
        
        $this->_getResource()->saveProductTemplateRelation($template, $data);
        /*echo "data<pre>";
        print_r($data);
        $newValues = [];
        if(isset($data) && !empty($data['product'])){
            $products = $data['product'];
            foreach ($products as $key => $product){
                $newValues[] = $key;
            }
        }

        $originalValues = [];
        $originalValues = $template->getRelatedProducts()->getAllIds();
        if(!empty($originalValues)){
            echo "originalValues<pre>";
            print_r($originalValues);
            $commonValues = [];
            $insertValues = [];
            $commonValues = array_unique(array_merge($newValues, $originalValues));
            echo "commonValues<pre>";
            print_r($commonValues);
            if(isset($data) && !empty($data['product'])) {
                $products = $data['product'];
                foreach ($commonValues as $commonValue) {
                    if(isset($products) && !empty($products[$commonValue])){
                        $insertValues[$commonValue] = $data['product'][$commonValue];
                    }

                }
            }
            echo "insertValues<pre>";
            print_r($insertValues);
            die;
            $this->_getResource()->saveProductTemplateRelation($template, $insertValues);
        } else {
            $this->_getResource()->saveProductTemplateRelation($template, $data);
        }*/



        return $this;
    }



    /**
     * Retrieve linked product collection
     *
     * @return ProductCollection
     */
    public function getProductCollection($template)
    {
        $collection = $this->getCollection()->addTemplateFilter($template);
        return $collection;
    }
}
