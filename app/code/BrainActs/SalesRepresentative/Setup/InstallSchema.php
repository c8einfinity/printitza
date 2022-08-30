<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{

    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $this->createBrainactsSalesrepMember($installer);
        $this->createBrainactsSalesrepMemberCustomer($installer);
        $this->createBrainactsSalesrepMemberOrder($installer);
        $this->createBrainactsSalesrepMemberProduct($installer);

        $this->createBrainactsSalesrepReportCustomer($installer);
        $this->createBrainactsSalesrepReportOrder($installer);
        $this->createBrainactsSalesrepReportProduct($installer);

        $installer->endSetup();
    }

    private function createBrainactsSalesrepMember($installer)
    {
        $table = $installer->getConnection()->newTable(
            $installer->getTable('brainacts_salesrep_member')
        )->addColumn(
            'member_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true]
        )->addColumn(
            'firstname',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => '']
        )->addColumn(
            'lastname',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => '']
        )->addColumn(
            'user_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11,
            []
        )->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            1,
            ['nullable' => false, 'default' => '1']
        )->addColumn(
            'product_priority',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11,
            ['default' => '0']
        )->addColumn(
            'product_rate_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11,
            ['default' => '0']
        )->addColumn(
            'product_value',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            [8, 4],
            ['default' => '0']
        )->addColumn(
            'customer_priority',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11,
            ['default' => '0']
        )->addColumn(
            'customer_rate_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11,
            ['default' => '0']
        )->addColumn(
            'customer_value',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            [8, 4],
            ['default' => '0']
        )->addColumn(
            'region_priority',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11,
            ['default' => '0']
        )->addColumn(
            'region_rate_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11,
            ['default' => '0']
        )->addColumn(
            'region_value',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            [8, 4],
            ['default' => '0']
        )->addColumn(
            'order_rate_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11,
            ['default' => '0']
        )->addColumn(
            'order_value',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            [8, 4],
            ['default' => '0']
        );
        $installer->getConnection()->createTable($table);
    }

    private function createBrainactsSalesrepMemberCustomer($installer)
    {
        $table = $installer->getConnection()->newTable(
            $installer->getTable('brainacts_salesrep_member_customer')
        )->addColumn(
            'member_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            ['unsigned' => true, 'identity' => true]
        )->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11,
            []
        )->addIndex(
            $installer->getIdxName('brainacts_salesrep_member_customer', ['member_id']),
            ['member_id']
        )->addForeignKey(
            $installer->getFkName(
                'brainacts_salesrep_member_customer',
                'member_id',
                'brainacts_salesrep_member',
                'member_id'
            ),
            'member_id',
            $installer->getTable('brainacts_salesrep_member'),
            'member_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );
        $installer->getConnection()->createTable($table);
    }

    private function createBrainactsSalesrepMemberOrder($installer)
    {
        $table = $installer->getConnection()->newTable(
            $installer->getTable('brainacts_salesrep_member_order')
        )->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11,
            ['unsigned' => true, 'identity' => true, 'nullable' => false, 'primary' => true]
        )->addColumn(
            'member_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            ['unsigned' => true]
        )->addColumn(
            'order_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11,
            []
        )->addColumn(
            'rule',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            1,
            ['default' => '0']
        )->addIndex(
            $installer->getIdxName('brainacts_salesrep_member_order', ['member_id']),
            ['member_id']
        )->addForeignKey(
            $installer->getFkName(
                'brainacts_salesrep_member_order',
                'member_id',
                'brainacts_salesrep_member',
                'member_id'
            ),
            'member_id',
            $installer->getTable('brainacts_salesrep_member'),
            'member_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );
        $installer->getConnection()->createTable($table);
    }

    private function createBrainactsSalesrepMemberProduct($installer)
    {
        $table = $installer->getConnection()->newTable(
            $installer->getTable('brainacts_salesrep_member_product')
        )->addColumn(
            'member_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            ['unsigned' => true, 'identity' => true]
        )->addColumn(
            'product_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11,
            ['unsigned' => true]
        )->addIndex(
            $installer->getIdxName('brainacts_salesrep_member_product', ['member_id']),
            ['member_id']
        )->addForeignKey(
            $installer->getFkName(
                'brainacts_salesrep_member_product',
                'member_id',
                'brainacts_salesrep_member',
                'member_id'
            ),
            'member_id',
            $installer->getTable('brainacts_salesrep_member'),
            'member_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );
        $installer->getConnection()->createTable($table);
    }

    private function createBrainactsSalesrepReportCustomer($installer)
    {
        $table = $installer->getConnection()->newTable(
            $installer->getTable('brainacts_salesrep_report_customer')
        )->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11,
            ['identity' => true,'unsigned' => true, 'nullable' => false, 'primary' => true]
        )->addColumn(
            'period',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
            null,
            []
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            5,
            ['unsigned' => true, 'default' => '0']
        )->addColumn(
            'order_price',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            [12, 4],
            ['nullable' => false, 'default' => '0.0000']
        )->addColumn(
            'member_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            ['unsigned' => true]
        )->addColumn(
            'order_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            []
        )->addColumn(
            'rate_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            1,
            ['default' => '0']
        )->addColumn(
            'rate_value',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            [12, 4],
            ['default' => '0']
        )->addColumn(
            'earn',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            [12, 4],
            ['default' => '0']
        )->addColumn(
            'increment_order_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            32,
            []
        )->addIndex(
            $installer->getIdxName('brainacts_salesrep_report_customer', ['member_id']),
            ['member_id']
        )->addForeignKey(
            $installer->getFkName(
                'brainacts_salesrep_report_customer',
                'member_id',
                'brainacts_salesrep_member',
                'member_id'
            ),
            'member_id',
            $installer->getTable('brainacts_salesrep_member'),
            'member_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );
        $installer->getConnection()->createTable($table);
    }

    private function createBrainactsSalesrepReportOrder($installer)
    {
        $table = $installer->getConnection()->newTable(
            $installer->getTable('brainacts_salesrep_report_order')
        )->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11,
            ['identity' => true,'unsigned' => true, 'nullable' => false, 'primary' => true]
        )->addColumn(
            'period',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
            null,
            []
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            5,
            ['unsigned' => true, 'default' => '0']
        )->addColumn(
            'order_price',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            [12, 4],
            ['nullable' => false, 'default' => '0.0000']
        )->addColumn(
            'member_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            ['unsigned' => true]
        )->addColumn(
            'order_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            []
        )->addColumn(
            'rate_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            1,
            ['default' => '0']
        )->addColumn(
            'rate_value',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            [12, 4],
            ['default' => '0']
        )->addColumn(
            'earn',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            [12, 4],
            ['default' => '0']
        )->addColumn(
            'increment_order_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            32,
            []
        )->addIndex(
            $installer->getIdxName('brainacts_salesrep_report_order', ['member_id']),
            ['member_id']
        )->addForeignKey(
            $installer->getFkName(
                'brainacts_salesrep_report_order',
                'member_id',
                'brainacts_salesrep_member',
                'member_id'
            ),
            'member_id',
            $installer->getTable('brainacts_salesrep_member'),
            'member_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );
        $installer->getConnection()->createTable($table);
    }

    private function createBrainactsSalesrepReportProduct($installer)
    {
        $table = $installer->getConnection()->newTable(
            $installer->getTable('brainacts_salesrep_report_product')
        )->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            11,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true]
        )->addColumn(
            'period',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
            null,
            []
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            5,
            ['unsigned' => true, 'default' => '0']
        )->addColumn(
            'product_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            ['unsigned' => true]
        )->addColumn(
            'product_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false, 'default' => '']
        )->addColumn(
            'product_price',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            [12, 4],
            ['nullable' => false, 'default' => '0.0000']
        )->addColumn(
            'qty_ordered',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            [12, 4],
            ['nullable' => false, 'default' => '0.0000']
        )->addColumn(
            'member_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            ['unsigned' => true]
        )->addColumn(
            'order_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            []
        )->addColumn(
            'rate_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            1,
            ['default' => '0']
        )->addColumn(
            'rate_value',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            [12, 4],
            ['default' => '0']
        )->addColumn(
            'earn',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            [12, 4],
            ['default' => '0']
        )->addColumn(
            'increment_order_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            32,
            []
        )->addColumn(
            'order_item_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            10,
            ['default' => '0']
        )->addIndex(
            $installer->getIdxName('brainacts_salesrep_report_product', ['member_id']),
            ['member_id']
        )->addForeignKey(
            $installer->getFkName(
                'brainacts_salesrep_report_product',
                'member_id',
                'brainacts_salesrep_member',
                'member_id'
            ),
            'member_id',
            $installer->getTable('brainacts_salesrep_member'),
            'member_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );
        $installer->getConnection()->createTable($table);
    }
}
