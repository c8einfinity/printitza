<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Clipart\Model\ResourceModel\Category;

/**
 * Clipart category resource model
 */
class Product extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * Magento string lib
     *
     * @var \Magento\Framework\Stdlib\StringUtils
     */
    protected $string;
    
    /**
     * @var \Designnbuy\Clipart\Model\ResourceModel\Category\Collection
     */
    protected $categoryCollectionFactory;
    
    /**
     * Construct
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param string|null $resourcePrefix
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Designnbuy\Clipart\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        $resourcePrefix = null
    ) {
        parent::__construct($context, $resourcePrefix);
        $this->string = $string;
        $this->dateTime = $dateTime;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
    }

    /**
     * Initialize resource model
     * Get tablename from config
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('designnbuy_clipart_category_relatedproduct', 'id');
    }

    /**
     * Save  product - product template relations
     *
     * @access public
     * @param Mage_Catalog_Model_Product $prooduct
     * @param array $data
     * @return Designnbuy_Templates_Model_Resource_Producttemplate_Product
     * @@author Ultimate Module Creator
     */
    public function saveProductRelation($product, $data)
    {

        if (!is_array($data)) {
            $data = array();
        }

        $tableName = 'designnbuy_clipart_category_relatedproduct';
        $table = $this->getTable($tableName);
        $where = ['related_id = ?' => (int)$product->getId()];
        $this->getConnection()->delete($table, $where);

        $categoryData = [];

        foreach ($data['clipart_category'] as $key => $category) {
            $categoryData[] = [
                'related_id' => (int)$product->getId(),
                'category_id' => $key,
                'position' => (isset($category['position'])) ? $category['position'] : 0
            ];
        }
        if(isset($categoryData) && !empty($categoryData)){
            $this->getConnection()->insertMultiple($table, $categoryData);
        }

        return $this;
    }

    /**
     * Retrieve product related Category
     * @return \Designnbuy\Clipart\Model\ResourceModel\Category\Collection
     */
    public function getRelatedClipartCategories($product)
    {
        if ($product instanceof \Magento\Catalog\Model\Product) {
            $productId = $product->getId();
        } elseif (is_numeric($product)) {
            $productId = $product;
        }
        
        $collection = $this->categoryCollectionFactory->create();

        /*if ($this->getStoreId()) {
            $collection->addStoreFilter($this->getStoreId());
        }*/

        $collection->getSelect()->joinLeft(
            ['rl' => $this->getTable('designnbuy_clipart_category_relatedproduct')],
            'main_table.category_id = rl.category_id',
            ['position']
        );
        if(isset($productId) && $productId != ''){
            $collection->getSelect()->where(
                'rl.related_id = ?',
                $productId
            );
        }
        
        return $collection;
    }
}
