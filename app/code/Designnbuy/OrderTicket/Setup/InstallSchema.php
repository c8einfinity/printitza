<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Designnbuy\OrderTicket\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var string
     */
    private static $salesConnectionName = 'sales';

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        /**
         * Prepare database before module installation
         */
        $setup->startSetup();

        /**
         * Create table 'designnbuy_orderticket'
         */
        $table = $setup->getConnection()->newTable(
            $setup->getTable('designnbuy_orderticket')
        )->addColumn(
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Order Ticket Id'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            32,
            [],
            'Status'
        )->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '1'],
            'Is Active'
        )->addColumn(
            'increment_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            50,
            [],
            'Increment Id'
        )->addColumn(
            'date_requested',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Order Ticket Requested At'
        )->addColumn(
            'order_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Order Id'
        )->addColumn(
            'order_increment_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            50,
            [],
            'Order Increment Id'
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true],
            'Store Id'
        )->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true],
            'Customer Id'
        )->addColumn(
            'customer_custom_email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Customer Custom Email'
        )->addColumn(
            'protect_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Protect Code'
        )->addIndex(
            $setup->getIdxName('designnbuy_orderticket', ['status']),
            ['status']
        )->addIndex(
            $setup->getIdxName('designnbuy_orderticket', ['is_active']),
            ['is_active']
        )->addIndex(
            $setup->getIdxName(
                'designnbuy_orderticket',
                ['increment_id', 'store_id'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['increment_id', 'store_id'],
            ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addIndex(
            $setup->getIdxName('designnbuy_orderticket', ['date_requested']),
            ['date_requested']
        )->addIndex(
            $setup->getIdxName('designnbuy_orderticket', ['order_id']),
            ['order_id']
        )->addIndex(
            $setup->getIdxName('designnbuy_orderticket', ['order_increment_id']),
            ['order_increment_id']
        )->addIndex(
            $setup->getIdxName('designnbuy_orderticket', ['store_id']),
            ['store_id']
        )->addIndex(
            $setup->getIdxName('designnbuy_orderticket', ['customer_id']),
            ['customer_id']
        )->addForeignKey(
            $setup->getFkName('designnbuy_orderticket', 'customer_id', 'customer_entity', 'entity_id'),
            'customer_id',
            $setup->getTable('customer_entity'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_SET_NULL
        )->addForeignKey(
            $setup->getFkName('designnbuy_orderticket', 'store_id', 'store', 'store_id'),
            'store_id',
            $setup->getTable('store'),
            'store_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_SET_NULL
        )->setComment(
            'Order Ticket LIst'
        );
        $setup->getConnection()->createTable($table);

        /**
         * Create table 'designnbuy_orderticket_grid'
         */
        $table = $setup->getConnection()->newTable(
            $setup->getTable('designnbuy_orderticket_grid')
        )->addColumn(
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Order Ticket Id'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            32,
            [],
            'Status'
        )->addColumn(
            'increment_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            50,
            [],
            'Increment Id'
        )->addColumn(
            'date_requested',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Order Ticket Requested At'
        )->addColumn(
            'order_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Order Id'
        )->addColumn(
            'order_increment_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            50,
            [],
            'Order Increment Id'
        )->addColumn(
            'order_date',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Order Created At'
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true],
            'Store Id'
        )->addColumn(
            'customer_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true],
            'Customer Id'
        )->addColumn(
            'customer_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Customer Billing Name'
        )->addIndex(
            $setup->getIdxName('designnbuy_orderticket_grid', ['status']),
            ['status']
        )->addIndex(
            $setup->getIdxName(
                'designnbuy_orderticket_grid',
                ['increment_id', 'store_id'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['increment_id', 'store_id'],
            ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addIndex(
            $setup->getIdxName('designnbuy_orderticket_grid', ['date_requested']),
            ['date_requested']
        )->addIndex(
            $setup->getIdxName('designnbuy_orderticket_grid', ['order_id']),
            ['order_id']
        )->addIndex(
            $setup->getIdxName('designnbuy_orderticket_grid', ['order_increment_id']),
            ['order_increment_id']
        )->addIndex(
            $setup->getIdxName('designnbuy_orderticket_grid', ['order_date']),
            ['order_date']
        )->addIndex(
            $setup->getIdxName('designnbuy_orderticket_grid', ['store_id']),
            ['store_id']
        )->addIndex(
            $setup->getIdxName('designnbuy_orderticket_grid', ['customer_id']),
            ['customer_id']
        )->addIndex(
            $setup->getIdxName('designnbuy_orderticket_grid', ['customer_name']),
            ['customer_name']
        )->addForeignKey(
            $setup->getFkName('designnbuy_orderticket_grid', 'entity_id', 'designnbuy_orderticket', 'entity_id'),
            'entity_id',
            $setup->getTable('designnbuy_orderticket'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Order Ticket Grid'
        );
        $setup->getConnection()->createTable($table);

        /**
         * Create table 'designnbuy_orderticket_status_history'
         */
        $table = $setup->getConnection()->newTable(
            $setup->getTable('designnbuy_orderticket_status_history')
        )->addColumn(
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Entity Id'
        )->addColumn(
            'orderticket_entity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Order Ticket Entity Id'
        )->addColumn(
            'is_customer_notified',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [],
            'Is Customer Notified'
        )->addColumn(
            'is_visible_on_front',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'Is Visible On Front'
        )->addColumn(
            'comment',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'Comment'
        )->addColumn(
            'file',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '64k',
            [],
            'File'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            32,
            [],
            'Status'
        )->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
            'Created At'
        )->addColumn(
            'is_admin',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            [],
            'Is this Merchant Comment'
        )->addIndex(
            $setup->getIdxName('designnbuy_orderticket_status_history', ['orderticket_entity_id']),
            ['orderticket_entity_id']
        )->addIndex(
            $setup->getIdxName('designnbuy_orderticket_status_history', ['created_at']),
            ['created_at']
        )->addForeignKey(
            $setup->getFkName('designnbuy_orderticket_status_history', 'orderticket_entity_id', 'designnbuy_orderticket', 'entity_id'),
            'orderticket_entity_id',
            $setup->getTable('designnbuy_orderticket'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Order Ticket status history designnbuy_orderticket_status_history'
        );
        $setup->getConnection()->createTable($table);

        

        $setup->endSetup();
    }
}
