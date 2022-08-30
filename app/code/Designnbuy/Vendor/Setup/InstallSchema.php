<?php


namespace Designnbuy\Vendor\Setup;

use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\InstallSchemaInterface;

class InstallSchema implements InstallSchemaInterface
{

    /**
     * {@inheritdoc}
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();

        $table_designnbuy_user = $setup->getConnection()->newTable($setup->getTable('designnbuy_vendor_user'));


        $table_designnbuy_user->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('identity' => true,'nullable' => false,'primary' => true,'unsigned' => true),
            'User ID'
        )->addColumn(
            'user_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false],
            'User ID'
        )->addColumn(
            'role_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false],
            'Role Id'
        )->addColumn(
            'notify_user',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1'],
            'Notify User by Mail'
        )->addColumn(
            'commission_percentage',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => true],
            'Commission Percentage'
        )->addColumn(
            'folder_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Folder Name'
        )->addColumn(
            'remote_host',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Remote Host'
        )->addColumn(
            'ftp_port',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'FTP Port'
        )->addColumn(
            'ftp_username',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'FTP Username'
        )->addColumn(
            'ftp_password',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'FTP Password'
        )->addColumn(
            'ftp_path',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'FTP Path'
        )->addColumn(
            'connection_timeout',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Connection Timeout'
        )->addColumn(
            'passive_ftp',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '0'],
            'Passive FTP'
        )->addColumn(
            'status',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1'],
            'status'
        )->addColumn(
            'creation_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Creation Time'
        )->addColumn(
            'update_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Modification Time'
        );

        $setup->getConnection()->createTable($table_designnbuy_user);


        $table_designnbuy_transaction = $setup->getConnection()->newTable($setup->getTable('designnbuy_vendor_transaction'));
        $table_designnbuy_transaction->addColumn(
            'transaction_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,),
            'Entity ID'
        );
        $table_designnbuy_transaction->addColumn(
            'vendor_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'primary' => false,'unsigned' => true],
            'Vendor ID'
        )->addColumn(
            'order_increment_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            50,
            [],
            'Order Increment Id'
        )->addColumn(
            'information',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Information'
        )->addColumn(
            'amount',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => true],
            'Amount'
        )->addColumn(
            'type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Type'
        )->addColumn(
            'creation_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Creation Time'
        )->addColumn(
            'update_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Modification Time'
        )->addIndex(
            $setup->getIdxName('designnbuy_vendor_transaction', ['order_increment_id']),
            ['order_increment_id']
        )->addForeignKey(
            $installer->getFkName('designnbuy_vendor_transaction', 'vendor_id', 'designnbuy_vendor_user', 'id'),
            'vendor_id',
            $installer->getTable('designnbuy_vendor_user'),
            'id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );

        $setup->getConnection()->createTable($table_designnbuy_transaction);


        $setup->endSetup();
    }
}
