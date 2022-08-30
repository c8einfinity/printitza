<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Pricecalculator\Model\ResourceModel\Product\Attribute\Backend;


/**
 * Catalog product tier price backend attribute model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class SquarePrice extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize connection and define main table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('designnbuy_height_width_square_price', 'value_id');
    }

    /**
     * Add qty column
     *
     * @param array $columns
     * @return array
     */
    protected function _loadPriceDataColumns($columns)
    {
        $columns['price_qty'] = 'qty';
        //$columns['percentage_value'] = 'percentage_value';
        return $columns;
    }

    /**
     * Order by qty
     *
     * @param \Magento\Framework\DB\Select $select
     * @return \Magento\Framework\DB\Select
     */
    protected function _loadPriceDataSelect($select)
    {
        $select->order('qty');
        return $select;
    }

    /**
     * Load Tier Prices for product
     *
     * @param int $productId
     * @param int $websiteId
     * @return array
     */
    public function loadPriceData($productId, $websiteId = null)
    {
        $select = $this->getSelect($websiteId);
        $productIdFieldName = $this->getProductIdFieldName();
        $select->where("{$productIdFieldName} = ?", $productId);

        $this->_loadPriceDataSelect($select);

        return $this->getConnection()->fetchAll($select);
    }

    /**
     * @param int|null $websiteId
     * @return \Magento\Framework\DB\Select
     */
    public function getSelect($websiteId = null)
    {
        $columns = [
            'price_id' => $this->getIdFieldName(),
            'website_id' => 'website_id',
            'all_groups' => 'all_groups',
            'cust_group' => 'customer_group_id',
            'price' => 'value',
        ];

        $columns = $this->_loadPriceDataColumns($columns);

        $select = $this->getConnection()->select()
            ->from($this->getMainTable(), $columns);

        if ($websiteId !== null) {
            if ($websiteId == '0') {
                $select->where('website_id = ?', $websiteId);
            } else {
                $select->where('website_id IN(?)', [0, $websiteId]);
            }
        }
        return $select;
    }

    /**
     * @return string
     */
    protected function getProductIdFieldName()
    {
        $table = $this->getTable('catalog_product_entity');
        $indexList = $this->getConnection()->getIndexList($table);
        return $indexList[$this->getConnection()->getPrimaryKeyName($table)]['COLUMNS_LIST'][0];
    }


    /**
     * Delete Tier Prices for product
     *
     * @param int $productId
     * @param int $websiteId
     * @param int $priceId
     * @return int The number of affected rows
     */
    public function deletePriceData($productId, $websiteId = null, $priceId = null)
    {
        $connection = $this->getConnection();

        $conds = [$connection->quoteInto($this->getProductIdFieldName() . ' = ?', $productId)];

        if ($websiteId !== null) {
            $conds[] = $connection->quoteInto('website_id = ?', $websiteId);
        }

        if ($priceId !== null) {
            $conds[] = $connection->quoteInto($this->getIdFieldName() . ' = ?', $priceId);
        }

        $where = implode(' AND ', $conds);

        return $connection->delete($this->getMainTable(), $where);
    }

    /**
     * Save tier price object
     *
     * @param \Magento\Framework\DataObject $priceObject
     * @return \Magento\Catalog\Model\ResourceModel\Product\Attribute\Backend\Tierprice
     */
    public function savePriceData(\Magento\Framework\DataObject $priceObject)
    {
        $connection = $this->getConnection();
        $data = $this->_prepareDataForTable($priceObject, $this->getMainTable());

        if (!empty($data[$this->getIdFieldName()])) {
            $where = $connection->quoteInto($this->getIdFieldName() . ' = ?', $data[$this->getIdFieldName()]);
            unset($data[$this->getIdFieldName()]);
            $connection->update($this->getMainTable(), $data, $where);
        } else {
            $connection->insert($this->getMainTable(), $data);
        }
        return $this;
    }

    /**
     * @param array $priceRow
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function validatePrice(array $priceRow)
    {
        if (!isset($priceRow['price']) || !$this->isPositiveOrZero($priceRow['price'])) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Group price must be a number greater than 0.')
            );
        }
    }
}
