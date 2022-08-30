<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OptionAdvancedPricing\Model\CollectionUpdater\Option\Value;

use MageWorx\OptionBase\Model\Product\Option\AbstractUpdater;
use MageWorx\OptionAdvancedPricing\Model\SpecialPrice as SpecialPriceModel;

class SpecialPrice extends AbstractUpdater
{
    /**
     * {@inheritdoc}
     */
    public function getFromConditions(array $conditions)
    {
        $alias = $this->getTableAlias();
        $table = $this->getTable($conditions);
        return [$alias => $table];
    }

    /**
     * {@inheritdoc}
     */
    public function getTableName($entityType)
    {
        if ($entityType == 'group') {
            return $this->resource->getTableName(SpecialPriceModel::OPTIONTEMPLATES_TABLE_NAME);
        }
        return $this->resource->getTableName(SpecialPriceModel::TABLE_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function getOnConditionsAsString()
    {
        return 'main_table.' . SpecialPriceModel::COLUMN_MAGEWORX_OPTION_TYPE_ID . ' = '
            . $this->getTableAlias() . '.' . SpecialPriceModel::COLUMN_MAGEWORX_OPTION_TYPE_ID;
    }

    /**
     * {@inheritdoc}
     */
    public function getColumns()
    {
        return [
            SpecialPriceModel::KEY_SPECIAL_PRICE => $this->getTableAlias() . '.' . SpecialPriceModel::KEY_SPECIAL_PRICE
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getTableAlias()
    {
        return $this->resource->getConnection()->getTableName('option_value_special_price');
    }

    /**
     * Get table for from conditions
     *
     * @param array $conditions
     * @return \Zend_Db_Expr
     */
    private function getTable($conditions)
    {
        $entityType = $conditions['entity_type'];
        $tableName  = $this->getTableName($entityType);

        $this->resource->getConnection()->query('SET SESSION group_concat_max_len = 100000;');

        $selectExpr = "SELECT mageworx_option_type_id,"
            . " CONCAT('[',"
            . " GROUP_CONCAT(CONCAT("
            . "'{\"price\"',':\"',IFNULL(price,''),'\",',"
            . "'\"customer_group_id\"',':\"',customer_group_id,'\",',"
            . "'\"price_type\"',':\"',price_type,'\",',"
            . "'\"date_from\"',':\"',IFNULL(date_from,''),'\",',"
            . "'\"date_to\"',':\"',IFNULL(date_to,''),'\",',"
            . "'\"comment\"',':\"',comment,'\"}'"
            . ")),"
            . "']')"
            . " AS special_price FROM " . $tableName;

        if ($conditions && (!empty($conditions['option_id']) || !empty($conditions['value_id']))) {
            $mageworxOptionTypeIds = $this->helper->findMageWorxOptionTypeIdByConditions($conditions);

            if ($mageworxOptionTypeIds) {
                $selectExpr .= " WHERE mageworx_option_type_id IN(" . implode(',', $mageworxOptionTypeIds) . ")";
            }
        }
        $selectExpr .= " GROUP BY mageworx_option_type_id";

        return new \Zend_Db_Expr('(' . $selectExpr . ')');
    }
}
