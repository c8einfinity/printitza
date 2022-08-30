<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{

    /**
     * Upgrades DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     * @throws \Zend_Db_Exception
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '2.2.0') < 0) {
            $this->createBrainActsSalesrepReportProfit($setup);
        }
        if (version_compare($context->getVersion(), '2.3.0') < 0) {
            $this->addSrToQuote($setup);
            $this->addConfirmationTable($setup);
            $this->addAdditionFieldMember($setup);
        }

        if (version_compare($context->getVersion(), '2.6.1') < 0) {
            $this->addWithdrawals($setup);
        }
        if (version_compare($context->getVersion(), '2.6.2') < 0) {
            $this->addWithdrawalsLinks($setup);
        }
        $setup->endSetup();
    }

    /**
     * Create table 'brainacts_salesrep_report_profit'
     * @param SchemaSetupInterface $installer
     * @throws \Zend_Db_Exception
     */
    private function createBrainActsSalesrepReportProfit(SchemaSetupInterface $installer)
    {

        $table = $installer->getConnection()->newTable(
            $installer->getTable('brainacts_salesrep_report_profit')
        )->addColumn(
            'id',
            Table::TYPE_INTEGER,
            10,
            ['unsigned' => true, 'identity' => true, 'primary' => true, 'nullable' => false]
        )->addColumn(
            'period',
            Table::TYPE_DATE,
            null,
            ['nullable' => false, 'COMMENT' => '']
        )->addColumn(
            'store_id',
            Table::TYPE_SMALLINT,
            5,
            ['unsigned' => true, 'COMMENT' => '']
        )->addColumn(
            'member_id',
            Table::TYPE_INTEGER,
            10,
            ['unsigned' => true, 'COMMENT' => '']
        )->addColumn(
            'product_id',
            Table::TYPE_INTEGER,
            110,
            ['default' => null, 'COMMENT' => '']
        )->addColumn(
            'product_name',
            Table::TYPE_TEXT,
            255,
            ['default' => null, 'COMMENT' => '']
        )->addColumn(
            'product_price',
            Table::TYPE_DECIMAL,
            [12, 4],
            ['default' => null, 'COMMENT' => '']
        )->addColumn(
            'order_id',
            Table::TYPE_INTEGER,
            11,
            ['default' => null, 'COMMENT' => '']
        )->addColumn(
            'rate_type',
            Table::TYPE_SMALLINT,
            6,
            ['default' => '0', 'COMMENT' => '']
        )->addColumn(
            'rate_value',
            Table::TYPE_DECIMAL,
            [12, 4],
            ['default' => '0.0000', 'COMMENT' => '']
        )->addColumn(
            'increment_order_id',
            Table::TYPE_TEXT,
            32,
            ['default' => null, 'COMMENT' => '']
        )->addColumn(
            'qty_ordered',
            Table::TYPE_DECIMAL,
            [12, 4],
            ['default' => null, 'COMMENT' => '']
        )->addColumn(
            'order_price',
            Table::TYPE_DECIMAL,
            [12, 4],
            ['default' => null, 'COMMENT' => '']
        )->addColumn(
            'earn',
            Table::TYPE_DECIMAL,
            [12, 4],
            ['default' => null, 'COMMENT' => '']
        )->addColumn(
            'order_item_id',
            Table::TYPE_INTEGER,
            11,
            ['default' => null, 'COMMENT' => '']
        )->addColumn(
            'rule_type',
            Table::TYPE_SMALLINT,
            2,
            ['default' => null, 'COMMENT' => '']
        )->addIndex(
            $installer->getIdxName('brainacts_salesrep_report_profit', 'store_id'),
            ['store_id']
        )->addIndex(
            $installer->getIdxName('brainacts_salesrep_report_profit', 'member_id'),
            ['member_id']
        )->addForeignKey(
            $installer->getFkName('brainacts_salesrep_report_profit', 'store_id', 'store', 'store_id'),
            'store_id',
            $installer->getTable('store'),
            'store_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('brainacts_salesrep_report_profit', 'member_id', 'brainacts_salesrep_member', 'member_id'),
            'member_id',
            $installer->getTable('brainacts_salesrep_member'),
            'member_id',
            Table::ACTION_CASCADE
        );
        $installer->getConnection()->createTable($table);
    }

    /**
     * Add Additional field to Quote Table
     * @param SchemaSetupInterface $installer
     */
    public function addSrToQuote(SchemaSetupInterface $installer)
    {
        $connection = $installer->getConnection();

        $column = [
            'type' => Table::TYPE_INTEGER,
            'length' => '11',
            'nullable' => false,
            'comment' => 'SR Member Id'
        ];
        $connection->addColumn($installer->getTable('quote'), 'sales_representative_id', $column);
    }

    /**
     * Add Field to member table to store additional information
     * @param SchemaSetupInterface $installer
     */
    public function addAdditionFieldMember(SchemaSetupInterface $installer)
    {
        $connection = $installer->getConnection();

        $column = [
            'type' => Table::TYPE_TEXT,
            'length' => '64k',
            'nullable' => true,
            'comment' => 'Additional information for customer'
        ];

        $connection->addColumn($installer->getTable('brainacts_salesrep_member'), 'comment', $column);
    }

    /**
     * Create 'brainacts_salesrep_confirm' table
     * @param SchemaSetupInterface $installer
     * @throws \Zend_Db_Exception
     */
    public function addConfirmationTable(SchemaSetupInterface $installer)
    {

        $table = $installer->getConnection()->newTable(
            $installer->getTable('brainacts_salesrep_confirm')
        )->addColumn(
            'confirm_id',
            Table::TYPE_INTEGER,
            10,
            ['unsigned' => true, 'identity' => true, 'primary' => true, 'nullable' => false]
        )->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            ['nullable' => false]
        )->addColumn(
            'from',
            Table::TYPE_TEXT,
            '64k',
            ['nullable' => false, 'COMMENT' => 'Serialized Members ID']
        )->addColumn(
            'to',
            Table::TYPE_TEXT,
            '64k',
            ['nullable' => false, 'COMMENT' => 'Serialized Members ID']
        )->addColumn(
            'creation_time',
            Table::TYPE_TIMESTAMP,
            null,
            [
                'nullable' => false,
                'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT,
                'COMMENT' => 'Created Time'
            ]
        );

        $installer->getConnection()->createTable($table);
    }

    /**
     * Create 'brainacts_salesrep_withdrawals' table
     * @param SchemaSetupInterface $installer
     * @throws \Zend_Db_Exception
     */
    private function addWithdrawals(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()->newTable(
            $installer->getTable('brainacts_salesrep_withdrawals')
        )->addColumn(
            'withdrawal_id',
            Table::TYPE_INTEGER,
            10,
            ['unsigned' => true, 'identity' => true, 'primary' => true, 'nullable' => false]
        )->addColumn(
            'member_id',
            Table::TYPE_INTEGER,
            10,
            ['unsigned' => true, 'COMMENT' => '']
        )->addColumn(
            'amount',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            [12, 4],
            ['nullable' => false, 'default' => '0.0000']
        )->addColumn(
            'creation_time',
            Table::TYPE_TIMESTAMP,
            null,
            [
                'nullable' => false,
                'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT,
                'COMMENT' => 'Created Time'
            ]
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            1,
            ['nullable' => false, 'default' => '1']
        )->addColumn(
            'comment',
            Table::TYPE_TEXT,
            '64k',
            ['nullable' => true]
        )->addForeignKey(
            $installer->getFkName('brainacts_salesrep_withdrawals', 'member_id', 'brainacts_salesrep_member', 'member_id'),
            'member_id',
            $installer->getTable('brainacts_salesrep_member'),
            'member_id',
            Table::ACTION_CASCADE
        );

        $installer->getConnection()->createTable($table);
    }

    public function addWithdrawalsLinks(SchemaSetupInterface $installer)
    {

        $connection = $installer->getConnection();

        $column = [
            'type' => Table::TYPE_INTEGER,
            'length' => '10',
            'nullable' => true,
            'unsigned' => true,
            'comment' => 'Withdrawal ID'
        ];

        $connection->addColumn(
            $installer->getTable('brainacts_salesrep_report_profit'),
            'withdrawal_id',
            $column
        );

        $column = [
            'type' => Table::TYPE_DECIMAL,
            'length' => '12,4',
            'nullable' => true,
            'comment' => 'Withdrawal Amount'
        ];

        $connection->addColumn(
            $installer->getTable('brainacts_salesrep_report_profit'),
            'withdrawal',
            $column
        );
        $connection->addForeignKey(
            $installer->getFkName('brainacts_salesrep_report_profit', 'withdrawal_id', 'brainacts_salesrep_withdrawals', 'withdrawal_id'),
            $installer->getTable('brainacts_salesrep_report_profit'),
            'withdrawal_id',
            $installer->getTable('brainacts_salesrep_withdrawals'),
            'withdrawal_id',
            Table::ACTION_CASCADE

        );
    }
}
