<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OptionFeatures\Model\CollectionUpdater\Option;

use MageWorx\OptionBase\Model\Product\Option\AbstractUpdater;
use MageWorx\OptionFeatures\Model\OptionDescription;

class Description extends AbstractUpdater
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
            return $this->resource->getTableName(OptionDescription::OPTIONTEMPLATES_TABLE_NAME);
        }
        return $this->resource->getTableName(OptionDescription::TABLE_NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function getOnConditionsAsString()
    {
        return 'main_table.' . OptionDescription::COLUMN_NAME_MAGEWORX_OPTION_ID . ' = '
            . $this->getTableAlias() . '.' . OptionDescription::COLUMN_NAME_MAGEWORX_OPTION_ID;
    }

    /**
     * {@inheritdoc}
     */
    public function getColumns()
    {
        return [
            OptionDescription::COLUMN_NAME_DESCRIPTION =>
                $this->getTableAlias() . '.' . OptionDescription::COLUMN_NAME_DESCRIPTION
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getTableAlias()
    {
        return $this->resource->getConnection()->getTableName('option_description');
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

        $selectExpr = "SELECT mageworx_option_id,"
            . " CONCAT('[',"
            . " GROUP_CONCAT(CONCAT("
            . "'{\"store_id\"',':\"',IFNULL(store_id,''),'\",',"
            . "'\"description\"',':\"',IFNULL(description,''),'\"}'"
            . ")),"
            . "']')"
            . " AS description FROM " . $tableName;

        if (!empty($conditions['option_id'])) {
            $mageworxOptionIds = $this->helper->findMageWorxOptionIdByConditions($conditions);

            if ($mageworxOptionIds) {
                $selectExpr .= " WHERE mageworx_option_id IN(" . implode(',', $mageworxOptionIds) . ")";
            }
        }
        $selectExpr .= " GROUP BY mageworx_option_id";

        return new \Zend_Db_Expr('(' . $selectExpr . ')');
    }
}
