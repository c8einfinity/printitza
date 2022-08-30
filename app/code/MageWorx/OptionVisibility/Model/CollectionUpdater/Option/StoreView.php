<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace MageWorx\OptionVisibility\Model\CollectionUpdater\Option;

use MageWorx\OptionBase\Model\Product\Option\AbstractUpdater;
use MageWorx\OptionVisibility\Model\OptionStoreView as StoreViewModel;
use Magento\Framework\App\ResourceConnection;
use MageWorx\OptionBase\Helper\Data as Helper;
use MageWorx\OptionBase\Helper\System as SystemHelper;
use MageWorx\OptionVisibility\Helper\Data as VisibilityHelper;
use MageWorx\OptionBase\Helper\CustomerVisibility as CustomerHelper;

class StoreView extends AbstractUpdater
{
    const ALIAS_TABLE_STORE_VIEW = 'option_store_view';

    /**
     * @var VisibilityHelper
     */
    protected $visibilityHelper;

    /**
     * @var CustomerHelper
     */
    protected $customerHelper;

    /**
     * @var bool
     */
    protected $isVisibilityFilterRequired;

    /**
     * @var bool
     */
    protected $isVisibilityStoreView;

    /**
     * CustomerGroup constructor.
     *
     * @param ResourceConnection $resource
     * @param Helper $helper
     * @param SystemHelper $systemHelper
     * @param VisibilityHelper $visibilityHelper
     * @param CustomerHelper $customerHelper
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        ResourceConnection $resource,
        Helper $helper,
        SystemHelper $systemHelper,
        VisibilityHelper $visibilityHelper,
        CustomerHelper $customerHelper
    ) {
        $this->visibilityHelper           = $visibilityHelper;
        $this->customerHelper             = $customerHelper;
        $this->isVisibilityFilterRequired = $this->customerHelper->isVisibilityFilterRequired();
        $this->isVisibilityStoreView      = $this->visibilityHelper->isVisibilityStoreViewEnabled();

        parent::__construct($resource, $helper, $systemHelper);
    }

    /**
     * {@inheritdoc}
     *
     * @param array $conditions
     * @return array
     */
    public function getFromConditions(array $conditions)
    {
        if ($this->isVisibilityFilterRequired && $this->isVisibilityStoreView) {
            return [$this->getTableAlias() => $this->getTableName($conditions['entity_type'])];
        }

        $alias = $this->getTableAlias();
        $table = $this->getTable($conditions);

        return [$alias => $table];
    }

    /**
     * {@inheritdoc}
     *
     * @param string $entityType
     * @return string
     */
    public function getTableName($entityType)
    {
        if ($entityType == 'group') {
            return $this->resource->getTableName(StoreViewModel::OPTIONTEMPLATES_TABLE_NAME);
        }

        return $this->resource->getTableName(StoreViewModel::TABLE_NAME);
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getOnConditionsAsString()
    {
        if ($this->isVisibilityFilterRequired && $this->isVisibilityStoreView) {
            $customerStoreId = $this->customerHelper->getCurrentCustomerStoreId();
            $conditions      = 'main_table.' . StoreViewModel::COLUMN_NAME_MAGEWORX_OPTION_ID . ' = '
                . $this->getTableAlias() . '.' . StoreViewModel::COLUMN_NAME_MAGEWORX_OPTION_ID
                . " AND " . $this->getTableAlias() . "." . StoreViewModel::COLUMN_NAME_STORE_ID
                . " = '" . $customerStoreId . "'";

            return $conditions;
        }

        return 'main_table.' . StoreViewModel::COLUMN_NAME_MAGEWORX_OPTION_ID . ' = '
            . $this->getTableAlias() . '.' . StoreViewModel::COLUMN_NAME_MAGEWORX_OPTION_ID;
    }

    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public function getColumns()
    {
        if ($this->isVisibilityFilterRequired && $this->isVisibilityStoreView) {
            $customerStoreExpr = $this->resource->getConnection()->getCheckSql(
                'main_table.is_all_websites = 1',
                '1',
                'IF(' . self::ALIAS_TABLE_STORE_VIEW . '.' . StoreViewModel::COLUMN_NAME_STORE_ID . ' IS NULL,0,1)'
            );


            return [
                'visibility_by_customer_store_id' => $customerStoreExpr
            ];
        }

        return [
            StoreViewModel::KEY_STORE_VIEW => $this->getTableAlias() . '.' . StoreViewModel::KEY_STORE_VIEW
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function getTableAlias()
    {
        return $this->resource->getConnection()->getTableName(self::ALIAS_TABLE_STORE_VIEW);
    }

    /**
     * Get table for from conditions
     *
     * @param array $conditions
     * @return \Zend_Db_Expr
     */
    protected function getTable($conditions)
    {
        $entityType = $conditions['entity_type'];
        $tableName  = $this->getTableName($entityType);

        $this->resource->getConnection()->query('SET SESSION group_concat_max_len = 100000;');

        $selectExpr = "SELECT mageworx_option_id,"
            . " CONCAT('[',"
            . " GROUP_CONCAT(CONCAT("
            . "'{\"customer_store_id\"',':\"',IFNULL(customer_store_id,''),'\"}'"
            . ")),"
            . "']')"
            . " AS store_view FROM " . $tableName;

        if (!empty($conditions['option_id']) || !empty($conditions['value_id'])) {
            $mageworxOptionIds = $this->helper->findMageWorxOptionIdByConditions($conditions);

            if ($mageworxOptionIds) {
                $selectExpr .= " WHERE mageworx_option_id IN(" . implode(',', $mageworxOptionIds) . ")";
            }
        }
        $selectExpr .= " GROUP BY mageworx_option_id";

        return new \Zend_Db_Expr('(' . $selectExpr . ')');
    }
}