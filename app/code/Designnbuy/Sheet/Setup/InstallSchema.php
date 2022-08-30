<?php
/**
 * Copyright © Designnbuy (support@designnbuy.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Designnbuy\Sheet\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

/**
 * Sheet setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        /**
         * Create table 'designnbuy_sheet_size'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('designnbuy_sheet_size')
        )->addColumn(
            'size_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Size ID'
        )->addColumn(
            'title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Size Title'
        )->addColumn(
            'unit',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            100,
            ['nullable' => true, 'default' => null],
            'Size Unit'
        )->addColumn(
            'width',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Width'
        )->addColumn(
            'height',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Height'
        )->addColumn(
            'creation_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Size Creation Time'
        )->addColumn(
            'update_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Size Modification Time'
        )->addColumn(
            'position',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '0'],
            'Position'
        )->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1'],
            'Is Size Active'
        )->addIndex(
            $installer->getIdxName('designnbuy_sheet_size', ['unit']),
            ['unit']
        )->addIndex(
            $setup->getIdxName(
                $installer->getTable('designnbuy_sheet_size'),
                ['title', 'unit'],
                AdapterInterface::INDEX_TYPE_FULLTEXT
            ),
            ['title', 'unit'],
            ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
        )->setComment(
            'Designnbuy Sheet Size Table'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'designnbuy_sheet_size_store'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('designnbuy_sheet_size_store')
        )->addColumn(
            'size_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'primary' => true],
            'Size ID'
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Store ID'
        )->addIndex(
            $installer->getIdxName('designnbuy_sheet_size_store', ['store_id']),
            ['store_id']
        )->addForeignKey(
            $installer->getFkName('designnbuy_sheet_size_store', 'size_id', 'designnbuy_sheet_size', 'size_id'),
            'size_id',
            $installer->getTable('designnbuy_sheet_size'),
            'size_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('designnbuy_sheet_size_store', 'store_id', 'store', 'store_id'),
            'store_id',
            $installer->getTable('store'),
            'store_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Designnbuy Sheet Size To Store Linkage Table'
        );
        $installer->getConnection()->createTable($table);


        /**
         * Create table 'designnbuy_sheet_size_relatedproduct'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('designnbuy_sheet_size_relatedproduct')
        )->addColumn(
            'size_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'primary' => true],
            'Size ID'
        )->addColumn(
            'related_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Related Product ID'
        )->addColumn(
            'position',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => '0'],
            'Position'
        )->addIndex(
            $installer->getIdxName('designnbuy_sheet_size_relatedproduct', ['related_id']),
            ['related_id']
        )->addForeignKey(
            $installer->getFkName('designnbuy_sheet_size_relatedproduct', 'size_id', 'designnbuy_sheet_size', 'size_id'),
            'size_id',
            $installer->getTable('designnbuy_sheet_size'),
            'size_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('designnbuy_sheet_size_relatedproduct', 'related_id', 'catalog_product_entity', 'entity_id'),
            'related_id',
            $installer->getTable('catalog_product_entity'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Designnbuy Sheet Size To Product Linkage Table'
        );
        $installer->getConnection()->createTable($table);


        /**
         * Create table 'designnbuy_sheet_size_relatedproduct'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('designnbuy_sheet_size_relatedsize')
        )->addColumn(
            'size_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'primary' => true],
            'Size ID'
        )->addColumn(
            'related_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'primary' => true],
            'Related Size ID'
        )->addColumn(
            'position',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => '0'],
            'Position'
        )->addIndex(
            $installer->getIdxName('designnbuy_sheet_size_relatedproduct', ['related_id']),
            ['related_id']
        )->addForeignKey(
            $installer->getFkName('designnbuy_sheet_size_relatedproduct1', 'size_id', 'designnbuy_sheet_size', 'size_id'),
            'size_id',
            $installer->getTable('designnbuy_sheet_size'),
            'size_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('designnbuy_sheet_size_relatedproduct2', 'related_id', 'designnbuy_sheet_size', 'size_id'),
            'size_id',
            $installer->getTable('designnbuy_sheet_size'),
            'size_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Designnbuy Sheet Size To Size Linkage Table'
        );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
