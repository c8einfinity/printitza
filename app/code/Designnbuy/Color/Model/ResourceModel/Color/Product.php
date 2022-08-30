<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Color\Model\ResourceModel\Color;

/**
 * Color category resource model
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
     * @var \Designnbuy\Color\Model\ResourceModel\Color\Collection
     */
    protected $colorCollectionFactory;
    
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
        \Designnbuy\Color\Model\ResourceModel\Color\CollectionFactory $colorCollectionFactory,
        $resourcePrefix = null
    ) {
        parent::__construct($context, $resourcePrefix);
        $this->string = $string;
        $this->dateTime = $dateTime;
        $this->colorCollectionFactory = $colorCollectionFactory;
    }

    /**
     * Initialize resource model
     * Get tablename from config
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('designnbuy_color_color_relatedproduct', 'id');
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
        $tableName = 'designnbuy_color_color_relatedproduct';
        $table = $this->getTable($tableName);
        $where = ['related_id = ?' => (int)$product->getId()];
        $this->getConnection()->delete($table, $where);
        foreach ($data as $color) {
            $rowData[] = ['related_id' => (int)$product->getId(), 'color_id' => $color['id']];
        }

        $this->getConnection()->insertMultiple($table, $rowData);
        return $this;
    }

    /**
     * Retrieve color related colors
     * @return \Designnbuy\Color\Model\ResourceModel\Color\Collection
     */
    public function getRelatedColors($product)
    {
        if ($product instanceof \Magento\Catalog\Model\Product) {
            $productId = $product->getId();
        } elseif (is_numeric($product)) {
            $productId = $product;
        }
        
        $collection = $this->colorCollectionFactory->create();

        /*if ($this->getStoreId()) {
            $collection->addStoreFilter($this->getStoreId());
        }*/

        $collection->getSelect()->joinLeft(
            ['rl' => $this->getTable('designnbuy_color_color_relatedproduct')],
            'main_table.color_id = rl.color_id',
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
