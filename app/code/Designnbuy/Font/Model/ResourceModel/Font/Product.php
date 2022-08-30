<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Font\Model\ResourceModel\Font;

/**
 * Font category resource model
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
     * @var \Designnbuy\Font\Model\ResourceModel\Font\Collection
     */
    protected $fontCollectionFactory;
    
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
        \Designnbuy\Font\Model\ResourceModel\Font\CollectionFactory $fontCollectionFactory,
        $resourcePrefix = null
    ) {
        parent::__construct($context, $resourcePrefix);
        $this->string = $string;
        $this->dateTime = $dateTime;
        $this->fontCollectionFactory = $fontCollectionFactory;
    }

    /**
     * Initialize resource model
     * Get tablename from config
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('designnbuy_font_font_relatedproduct', 'id');
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
        $tableName = 'designnbuy_font_font_relatedproduct';
        $table = $this->getTable($tableName);
        $where = ['related_id = ?' => (int)$product->getId()];
        $this->getConnection()->delete($table, $where);
        /*foreach ($data as $font) {
            $rowData[] = ['related_id' => (int)$product->getId(), 'font_id' => $font['id']];
        }*/

        $fontData = [];

        foreach ($data['font'] as $key => $font) {
            $fontData[] = [
                'related_id' => (int)$product->getId(),
                'font_id' => $key,
                'position' => (isset($font['position'])) ? $font['position'] : 0
            ];
        }

        if(isset($fontData) && !empty($fontData)){
            $this->getConnection()->insertMultiple($table, $fontData);
        }
        return $this;
    }

    /**
     * Retrieve font related fonts
     * @return \Designnbuy\Font\Model\ResourceModel\Font\Collection
     */
    public function getRelatedFonts($product)
    {

        $collection = $this->fontCollectionFactory->create();

        /*if ($this->getStoreId()) {
            $collection->addStoreFilter($this->getStoreId());
        }*/

        $collection->getSelect()->joinLeft(
            ['rl' => $this->getTable('designnbuy_font_font_relatedproduct')],
            'main_table.font_id = rl.font_id',
            ['position']
        )->where(
            'rl.related_id = ?',
            $product->getId()
        );
        
        return $collection;
    }
}
