<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Plugin\AdditionalFields;

/**
 * Class CollectionFactory
 * @author BrainActs Core Team <support@brainacts.com>
 */
//@codingStandardsIgnoreFile
class CollectionFactory
{
    /**
     * @param \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory $subject
     * @param \Closure $proceed
     * @param $requestName
     * @return mixed
     */
    public function aroundGetReport(
        \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory $subject,
        \Closure $proceed,
        $requestName
    ) {
        $result = $proceed($requestName);
        if ($requestName == 'sales_order_grid_data_source') {
            if ($result instanceof \Magento\Sales\Model\ResourceModel\Order\Grid\Collection) {
                $tableMember = $result->getTable('brainacts_salesrep_member_order');
                $bsmTable = $result->getTable('brainacts_salesrep_member');
                $select = new \Zend_Db_Expr("(SELECT group_concat(firstname, ' ', lastname SEPARATOR ', ') FROM " . $tableMember .
                    " LEFT JOIN {$bsmTable} ON {$tableMember}.member_id = {$bsmTable}.member_id
                    
                    where order_id=main_table.entity_id)");

                //add code to join table, and mapping field to select
                $expr = 'CONCAT(related_member.firstname, " ", related_member.lastname)';
                $result->getSelect()->columns(['representative' => $select])
                    ->joinLeft(
                        ['related' => $result->getTable('brainacts_salesrep_member_order')],
                        'related.order_id = main_table.entity_id',
                        ['member_id']
                    )->joinLeft(
                        ['related_member' => $result->getTable('brainacts_salesrep_member')],
                        'related.member_id = related_member.member_id',
                        ['member_name' => new \Zend_Db_Expr($expr)]
                    )->group(['main_table.entity_id']);

                //echo $result->getSelect();die();
            }
        }

        return $result;
    }
}
