<?php
namespace Designnbuy\Productattach\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 * @package Designnbuy\Productattach\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        /*
         * Drop tables if exists
         */

        $installer->getConnection()->dropTable($installer->getTable('designnbuy_productattach'));
        $installer->getConnection()->dropTable($installer->getTable('designnbuy_productattach_fileicon'));

        /**
         * Creating table designnbuy_productattach
         */
        $table_designnbuy_productattach = $installer->getConnection()->newTable($installer->getTable('designnbuy_productattach'));
        
        $table_designnbuy_productattach->addColumn(
            'productattach_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Entity Id'
        );

        $table_designnbuy_productattach->addColumn(
            'name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Name'
        );

        $table_designnbuy_productattach->addColumn(
            'description',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true,'default' => null],
            'Description'
        );

        $table_designnbuy_productattach->addColumn(
            'file',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true,'default' => null],
            'Content'
        );

        $table_designnbuy_productattach->addColumn(
            'file_ext',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true,'default' => null],
            'File Extension'
        );

        $table_designnbuy_productattach->addColumn(
            'url',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => true,'default' => null],
            'Productattach image media path'
        );

        $table_designnbuy_productattach->addColumn(
            'store',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => true,'default' => null],
            'Store'
        );

        $table_designnbuy_productattach->addColumn(
            'customer_group',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => true,'default' => null],
            'Customer Group'
        );

        $table_designnbuy_productattach->addColumn(
            'products',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            ['nullable' => true,'default' => null],
            'Assigned Products'
        );

        $table_designnbuy_productattach->addColumn(
            'active',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => '0'],
            'Active'
        );

        $table_designnbuy_productattach->addColumn(
            'created_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            ['nullable' => false],
            'Created At'
        );

        $table_designnbuy_productattach->addColumn(
            'published_at',
            \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
            null,
            ['nullable' => true,'default' => null],
            'World publish date'
        );

        $table_designnbuy_productattach->addIndex(
            $installer->getIdxName(
                'Designnbuy_productattach',
                ['published_at'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX
            ),
            ['published_at'],
            ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_INDEX]
        )->setComment('Productattach item');

        $installer->getConnection()->createTable($table_designnbuy_productattach);

        $table_designnbuy_productattach_fileicon = $setup->getConnection()->newTable($setup->getTable('designnbuy_productattach_fileicon'));
        
        $table_designnbuy_productattach_fileicon->addColumn(
            'fileicon_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            array('identity' => true,'nullable' => false,'primary' => true,'unsigned' => true,),
            'Entity ID'
        );
        
        $table_designnbuy_productattach_fileicon->addColumn(
            'icon_ext',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'icon_ext'
        );
        
        $table_designnbuy_productattach_fileicon->addColumn(
            'icon_image',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            null,
            [],
            'icon_image'
        );

        $setup->getConnection()->createTable($table_designnbuy_productattach_fileicon);

        $installer->endSetup();
    }
}
