<?php
namespace Drc\Storepickup\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements  UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        if (version_compare($context->getVersion(), '1.0.1') < 0) 
        {
            $table = $installer->getConnection()->newTable(
            $installer->getTable('drc_storepickup_holidays')
            )->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'nullable' => false, 'primary'  => true],
                'Holiday ID'
            )->addColumn(
                'title',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable => false'],
                'Holiday Title'
            )->addColumn(
                'holiday_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable => false'],
                'Holiday Type'
            )->addColumn(
                'is_enable',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable => false'],
                'Holiday Enable'
            )->addColumn(
                'from_date',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [],
                'Holiday From Date'
            )->addColumn(
                'to_date',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [],
                'Holiday To Date'
            )->setComment('Storelocator Holidays Table');
            $installer->getConnection()->createTable($table);


            /**
             * Create table 'drc_storepickup_holidays_store'
             */
            $table = $installer->getConnection()->newTable(
                $installer->getTable('drc_storepickup_holidays_store')
            )->addColumn(
                'holidays_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'primary' => true],
                'Holidays ID'
            )->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store ID'
            )->addIndex(
                $installer->getIdxName('drc_storepickup_holidays_store', ['store_id']),
                ['store_id']
            )->addForeignKey(
                $installer->getFkName('drc_storepickup_holidays_store', 'holidays_id', 'drc_storepickup_holidays', 'entity_id'),
                'holidays_id',
                $installer->getTable('drc_storepickup_holidays'),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName('drc_storepickup_holidays_store', 'store_id', 'store', 'store_id'),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment(
                'Drc Storepickup Holidays To Store Linkage Table'
            );
            $installer->getConnection()->createTable($table);
        }

        $installer->endSetup();
    }
}