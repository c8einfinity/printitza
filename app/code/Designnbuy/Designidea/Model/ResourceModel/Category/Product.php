<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Designidea\Model\ResourceModel\Category;

/**
 * Category Product resource model
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
     * @var \Designnbuy\Designidea\Model\ResourceModel\Category\Collection
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
        \Designnbuy\Designidea\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
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
        $this->_init('designnbuy_designidea_category_relatedproduct', 'id');
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

        $tableName = 'designnbuy_designidea_category_relatedproduct';
        $table = $this->getTable($tableName);
        $where = ['related_id = ?' => (int)$product->getId()];
        $this->getConnection()->delete($table, $where);
        /*$rowData = [];

        foreach ($data['printingmethod'] as $id) {
            $id = (int)$id;
            $rowData[] = ['related_id' => (int)$product->getId(), 'printingmethod_id' => $id, 'position' => $data[$id]['position']];
        }*/
        $categoryData = [];

        foreach ($data['designidea_category'] as $key => $category) {
            $categoryData[] = [
                'related_id' => (int)$product->getId(),
                'category_id' => $key,
                'position' => (isset($category['position'])) ? $category['position'] : 0
            ];
            /*$id = (int)$id;
            $printingMethodData[] = array_merge(['related_id' => (int)$product->getId(), 'printingmethod_id' => $id], 'position' => (isset($data[$id]) && is_array($data[$id])) ? $data[$id] : 0,
                (isset($data[$id]) && is_array($data[$id])) ? $data[$id] : []
            );*/
        }

        if(isset($categoryData) && !empty($categoryData)){
            $this->getConnection()->insertMultiple($table, $categoryData);
        }

        return $this;
    }

    /**
     * Save product designidea - product relations
     *
     * @access public
     * @param Designnbuy\Designidea\Model\Designidea $designidea
     * @param array $data
     * @return Designnbuy\Designidea\Model\ResourceModel\Designidea
     * @author Ajay Makwana
     */
    public function saveProductDesignIdeaCategoryRelation($category, $data)
    {
        if (!is_array($data)) {
            $data = array();
        }
        $tableName = 'designnbuy_designidea_category_relatedproduct';
        $table = $this->getTable($tableName);
        $deleteCondition = $this->getConnection()->quoteInto('category_id=?', $category->getId());
        $this->getConnection()->delete($table, $deleteCondition);

        foreach ($data as $productId => $info) {
            $this->getConnection()->insert(
                $this->getMainTable(),
                array(
                    'category_id' => $category->getId(),
                    'related_id'    => $productId,
                    'position'      => @$info['position']
                )
            );
        }
        return $this;
    }

    /**
     * Retrieve printingmethod related printingmethods
     * @return \Designnbuy\PrintingMethod\Model\ResourceModel\PrintingMethod\Collection
     */
    public function getRelatedDesignIdeaCategories($product)
    {
        if ($product instanceof \Magento\Catalog\Model\Product) {
            $productId = $product->getId();
        } elseif (is_numeric($product)) {
            $productId = $product;
        }

        $collection = $this->categoryCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->joinAttribute('title','designnbuy_designidea_category/title','entity_id',null,'left',0);
        $collection->getSelect()->joinLeft(
            ['rl' => $this->getTable('designnbuy_designidea_category_relatedproduct')],
            'e.entity_id = rl.category_id',
            ['position']
        )->where(
            'rl.related_id = ?',
            $productId
        );
        $collection->setOrder('rl.position', 'ASC');
        return $collection;
    }
}
