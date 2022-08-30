<?php

namespace Unific\Connector\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.4.5', '<')) {
            $this->upgradeTo145($setup);
        }
        if (version_compare($context->getVersion(), '1.4.10', '<')) {
            $this->addQueueItemStatusColumn($setup);
        }

        $setup->endSetup();
    }

    public function upgradeTo145(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->dropTable($setup->getConnection()->getTableName('unific_connector_log'));
        $setup->getConnection()->dropTable($setup->getConnection()->getTableName('unific_Connector_audit_log'));

        /**
         * The MySQL Table to implement the AMQP behaviour
         */
        $table = $setup->getConnection()->newTable($setup->getTable('unific_Connector_message_queue'))
            ->addColumn('guid', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, [
                'nullable' => true,
            ], 'A unique GUID identifier')
            ->addColumn('message', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '2M', [
                'nullable' => true,
            ], 'Message')
            ->addColumn('headers', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, [
                'nullable' => false,
            ], 'Message')
            ->addColumn('url', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, [
                'nullable' => false,
            ], 'Message')
            ->addColumn('historical', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 0, [
                'nullable' => false,
                'default' => 0
            ], 'Message')
            ->addColumn('request_type', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, [
                'nullable' => false,
            ], 'The request type, can be POST, PUT, DELETE')
            ->addColumn('retry_amount', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 0, [
                'unsigned' => true,
                'nullable' => false,
                'default' => 0
            ], 'Group ID')
            ->addColumn('max_retry_amount', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 0, [
                'unsigned' => true,
                'nullable' => false,
                'default' => 20
            ], 'Group ID')
            ->addColumn('response_error', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, [
                'nullable' => true,
            ], 'Message')
            ->addColumn('response_http_code', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 0, [
                'unsigned' => true,
                'nullable' => false,
            ], 'Group ID')
            ->addColumn('request_date_first', \Magento\Framework\DB\Ddl\Table::TYPE_DATE, 0, [
                'nullable' => true,
            ], 'Date where this request was first sent')
            ->addColumn('request_date_last', \Magento\Framework\DB\Ddl\Table::TYPE_DATE, 0, [
                'nullable' => true,
            ], 'Date where this request was last sent')
            ->addColumn(
                'request_date_first',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Date where this request was first sent'
            )
            ->addColumn(
                'request_date_last',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                'Date where this request was last sent'
            )
            ->setComment(
                'Message Queue'
            );

        $setup->getConnection()->createTable($table);

        $table = $setup->getConnection()->newTable($setup->getTable('unific_connector_audit_log'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true,
                ],
                'ID'
            )
            ->addColumn('request_guid', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, [
                'nullable' => true,
            ], 'A unique GUID identifier')
            ->addColumn('request_url', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, [
                'nullable' => false,
            ], 'Message')
            ->addColumn('request_headers', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, [
                'nullable' => false,
            ], 'Request Headers')
            ->addColumn('request_message', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, [
                'nullable' => true,
            ], 'Request Message')
            ->addColumn('request_type', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, [
                'nullable' => false,
            ], 'The request type, can be POST, PUT, DELETE')

            ->addColumn('historical', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 0, [
                'nullable' => false,
                'default' => 0
            ], 'Is Historical')
            ->addColumn('retry_amount', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 0, [
                'unsigned' => true,
                'nullable' => false,
                'default' => 0
            ], 'Group ID')
            ->addColumn('response_http_code', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 0, [
                'unsigned' => true,
                'nullable' => false,
            ], 'Group ID')
            ->addColumn('response_headers', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 0, [
                'unsigned' => true,
                'nullable' => false,
            ], 'Group ID')
            ->addColumn('response_message', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, [
                'nullable' => true,
            ], 'Message')
            ->addColumn('message_status', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 0, [
                'nullable' => true,
            ], 'Message')
            ->addColumn(
                'date_created',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Date when the webhook log was created'
            )
            ->setComment(
                'Audit Log'
            );

        $setup->getConnection()->createTable($table);

        $setup->getConnection()->addColumn(
            $setup->getTable('unific_Connector_message_queue'),
            'type_id',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => 0,
                'comment' => 'ID Of the relation type'
            ]
        );

        $setup->getConnection()->addColumn(
            $setup->getTable('unific_Connector_message_queue'),
            'priority',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => 5,
                'comment' => 'Priority in the queue'
            ]
        );

        /**
         * Type: customer, order, category, product
         * Type queue ID: Latest ID which has been put in the queue
         */
        $table = $setup->getConnection()->newTable($setup->getTable('unific_connector_historical'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true,
                ],
                'ID'
            )
            ->addColumn(
                'historical_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                0,
                [
                    'nullable' => false,
                ],
                'Historical Queue Entity Type'
            )
            ->addColumn(
                'historical_type_page',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                0,
                [
                    'nullable' => false,
                    'default' => 0
                ],
                'Current Page for SearchCriteria'
            );

        $setup->getConnection()->createTable($table);

        /**
         * Add a locking mechanism so that there can not be multiple instances deadlocking eachother
         */
        $table = $setup->getConnection()->newTable($setup->getTable('unific_connector_locks'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true,
                ],
                'ID'
            )
            ->addColumn(
                'type',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                0,
                [
                    'nullable' => false,
                ],
                'Historical Lock Type'
            );

        $setup->getConnection()->createTable($table);
    }

    public function addQueueItemStatusColumn(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable('unific_Connector_message_queue'),
            'status',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => false,
                'default'   => 0,
                'comment' => 'Item Status'
            ]
        );
        $setup->getConnection()->addColumn(
            $setup->getTable('unific_Connector_message_queue'),
            'status_change',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'unsigned'  => true,
                'nullable'  => true,
                'default'   => null,
                'comment' => 'Status Change Timestamp'
            ]
        );
    }
}
