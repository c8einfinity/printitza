<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Background\Model\ResourceModel\Background;

/**
 * Background category resource model
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
     * @var \Designnbuy\Background\Model\ResourceModel\Background\Collection
     */
    protected $backgroundCollectionFactory;
    
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
        \Designnbuy\Background\Model\ResourceModel\Background\CollectionFactory $backgroundCollectionFactory,
        $resourcePrefix = null
    ) {
        parent::__construct($context, $resourcePrefix);
        $this->string = $string;
        $this->dateTime = $dateTime;
        $this->backgroundCollectionFactory = $backgroundCollectionFactory;
    }

    /**
     * Initialize resource model
     * Get tablename from config
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('designnbuy_background_background_relatedproduct', 'id');
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
        /*if (!is_array($data)) {
            $data = array();
        }
        $tableName = 'designnbuy_background_background_relatedproduct';
        $table = $this->getTable($tableName);
        $where = ['related_id = ?' => (int)$product->getId()];
        $this->getConnection()->delete($table, $where);
        foreach ($data as $background) {
            $rowData[] = ['related_id' => (int)$product->getId(), 'background_id' => $background['id']];
        }

        $this->getConnection()->insertMultiple($table, $rowData);
        return $this;*/

        if (!is_array($data)) {
            $data = array();
        }

        $tableName = 'designnbuy_background_background_relatedproduct';
        $table = $this->getTable($tableName);
        $where = ['related_id = ?' => (int)$product->getId()];
        $this->getConnection()->delete($table, $where);

        $backgroundData = [];

        foreach ($data['background'] as $key => $background) {
            $backgroundData[] = [
                'related_id' => (int)$product->getId(),
                'background_id' => $key,
                'position' => (isset($background['position'])) ? $background['position'] : 0
            ];
        }
        if(isset($backgroundData) && !empty($backgroundData)){
            $this->getConnection()->insertMultiple($table, $backgroundData);
        }

        return $this;
    }

    /**
     * Retrieve background related backgrounds
     * @return \Designnbuy\Background\Model\ResourceModel\Background\Collection
     */
    public function getRelatedBackgrounds($product)
    {
        if ($product instanceof \Magento\Catalog\Model\Product) {
            $productId = $product->getId();
        } elseif (is_numeric($product)) {
            $productId = $product;
        }
        
        $collection = $this->backgroundCollectionFactory->create();

        /*if ($this->getStoreId()) {
            $collection->addStoreFilter($this->getStoreId());
        }*/

        $collection->getSelect()->joinLeft(
            ['rl' => $this->getTable('designnbuy_background_background_relatedproduct')],
            'main_table.background_id = rl.background_id',
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
