<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\PrintingMethod\Model\ResourceModel\PrintingMethod;

/**
 * PrintingMethod resource model
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
     * @var \Designnbuy\PrintingMethod\Model\ResourceModel\PrintingMethod\Collection
     */
    protected $printingMethodCollectionFactory;
    
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
        \Designnbuy\PrintingMethod\Model\ResourceModel\PrintingMethod\CollectionFactory $printingMethodCollectionFactory,
        $resourcePrefix = null
    ) {
        parent::__construct($context, $resourcePrefix);
        $this->string = $string;
        $this->dateTime = $dateTime;
        $this->printingMethodCollectionFactory = $printingMethodCollectionFactory;
    }

    /**
     * Initialize resource model
     * Get tablename from config
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('designnbuy_printingmethod_printingmethod_relatedproduct', 'id');
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

        $tableName = 'designnbuy_printingmethod_printingmethod_relatedproduct';
        $table = $this->getTable($tableName);
        $where = ['related_id = ?' => (int)$product->getId()];
        $this->getConnection()->delete($table, $where);
        /*$rowData = [];

        foreach ($data['printingmethod'] as $id) {
            $id = (int)$id;
            $rowData[] = ['related_id' => (int)$product->getId(), 'printingmethod_id' => $id, 'position' => $data[$id]['position']];
        }*/
        $printingMethodData = [];

        foreach ($data['printingmethod'] as $key => $printingmethod) {
            $printingMethodData[] = [
                'related_id' => (int)$product->getId(),
                'printingmethod_id' => $key,
                'position' => (isset($printingmethod['position'])) ? $printingmethod['position'] : 0
            ];
            /*$id = (int)$id;
            $printingMethodData[] = array_merge(['related_id' => (int)$product->getId(), 'printingmethod_id' => $id], 'position' => (isset($data[$id]) && is_array($data[$id])) ? $data[$id] : 0,
                (isset($data[$id]) && is_array($data[$id])) ? $data[$id] : []
            );*/
        }

        if(isset($printingMethodData) && !empty($printingMethodData)){
            $this->getConnection()->insertMultiple($table, $printingMethodData);
        }

        return $this;
    }

    /**
     * Retrieve printingmethod related printingmethods
     * @return \Designnbuy\PrintingMethod\Model\ResourceModel\PrintingMethod\Collection
     */
    public function getRelatedPrintingMethods($product)
    {
        if ($product instanceof \Magento\Catalog\Model\Product) {
            $productId = $product->getId();
        } elseif (is_numeric($product)) {
            $productId = $product;
        }
        
        $collection = $this->printingMethodCollectionFactory->create()->addActiveFilter();

        $collection->getSelect()->joinLeft(
            ['rl' => $this->getTable('designnbuy_printingmethod_printingmethod_relatedproduct')],
            'main_table.printingmethod_id = rl.printingmethod_id',
            ['position']
        )->where(
            'rl.related_id = ?',
            $productId
        );
        $collection->setOrder('rl.position', 'ASC');
        return $collection;
    }
}
