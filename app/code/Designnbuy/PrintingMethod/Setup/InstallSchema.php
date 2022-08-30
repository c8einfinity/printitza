<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\PrintingMethod\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

/**
 * PrintingMethod setup
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
         * Create table 'designnbuy_printingmethod_printingmethod'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('designnbuy_printingmethod_printingmethod')
        )->addColumn(
            'printingmethod_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'PrintingMethod ID'
        )->addColumn(
            'title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'PrintingMethod Title'
        )->addColumn(
            'pricing_logic',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Pricing Logic'
        )->addColumn(
            'printable_colors',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Printable Colors'
        )->addColumn(
            'min_qty',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Minimum Quantity'
        )->addColumn(
            'max_qty',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Maximum Quantity'
        )->addColumn(
            'is_image_upload',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1'],
            'Is Image Upload'
        )->addColumn(
            'image_price',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Image Price'
        )->addColumn(
            'artwork_setup_price_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Artwork Setup Fee Type'
        )->addColumn(
            'artwork_setup_price',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            null,
            ['unsigned' => true, 'nullable' => false],
            ' Artwork Setup Fee'
        )->addColumn(
            'is_alert',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1'],
            'Is Alert'
        )->addColumn(
            'alert_message',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Alert Message'
        )->addColumn(
            'creation_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'PrintingMethod Creation Time'
        )->addColumn(
            'update_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'PrintingMethod Modification Time'
        )->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1'],
            'Is PrintingMethod Active'
        )->addColumn(
            'position',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false],
            'Position'
        )->addIndex(
            $setup->getIdxName(
                $installer->getTable('designnbuy_printingmethod_printingmethod'),
                ['title'],
                AdapterInterface::INDEX_TYPE_FULLTEXT
            ),
            ['title'],
            ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
        )->setComment(
            'Designnbuy PrintingMethod PrintingMethod Table'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'designnbuy_printingmethod_printingmethod_store'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('designnbuy_printingmethod_printingmethod_store')
        )->addColumn(
            'printingmethod_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'primary' => true],
            'PrintingMethod ID'
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Store ID'
        )->addIndex(
            $installer->getIdxName('designnbuy_printingmethod_printingmethod_store', ['store_id']),
            ['store_id']
        )->addForeignKey(
            $installer->getFkName('designnbuy_printingmethod_printingmethod_store', 'printingmethod_id', 'designnbuy_printingmethod_printingmethod', 'printingmethod_id'),
            'printingmethod_id',
            $installer->getTable('designnbuy_printingmethod_printingmethod'),
            'printingmethod_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('designnbuy_printingmethod_printingmethod_store', 'store_id', 'store', 'store_id'),
            'store_id',
            $installer->getTable('store'),
            'store_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Designnbuy PrintingMethod PrintingMethod To Store Linkage Table'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'designnbuy_printingmethod_printingmethod_label'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('designnbuy_printingmethod_printingmethod_label')
        )->addColumn(
            'label_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Label Id'
        )->addColumn(
            'printingmethod_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false],
            'PrintingMethod Id'
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Store Id'
        )->addColumn(
            'label',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Label'
        )->addIndex(
            $installer->getIdxName(
                'designnbuy_printingmethod_printingmethod_label',
                ['printingmethod_id', 'store_id'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['printingmethod_id', 'store_id'],
            ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addIndex(
            $installer->getIdxName('designnbuy_printingmethod_printingmethod_label', ['store_id']),
            ['store_id']
        )->addForeignKey(
            $installer->getFkName('designnbuy_printingmethod_printingmethod_label', 'printingmethod_id', 'designnbuy_printingmethod_printingmethod', 'printingmethod_id'),
            'printingmethod_id',
            $installer->getTable('designnbuy_printingmethod_printingmethod'),
            'printingmethod_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('designnbuy_printingmethod_printingmethod_label', 'store_id', 'store', 'store_id'),
            'store_id',
            $installer->getTable('store'),
            'store_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Designnbuy PrintingMethod PrintingMethod Label'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'designnbuy_printingmethod_printingmethod_relatedproduct'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('designnbuy_printingmethod_printingmethod_relatedproduct')
        )->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'ID'
        )->addColumn(
            'printingmethod_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'primary' => false],
            'PrintingMethod ID'
        )->addColumn(
            'related_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => false],
            'Related Product ID'
        )->addColumn(
            'position',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false],
            'Position'
        )->addIndex(
            $installer->getIdxName('designnbuy_printingmethod_printingmethod_relatedproduct', ['related_id']),
            ['related_id']
        )->addForeignKey(
            $installer->getFkName('designnbuy_printingmethod_printingmethod_relatedproduct', 'printingmethod_id', 'designnbuy_printingmethod_printingmethod', 'printingmethod_id'),
            'printingmethod_id',
            $installer->getTable('designnbuy_printingmethod_printingmethod'),
            'printingmethod_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('designnbuy_printingmethod_printingmethod_relatedproduct', 'related_id', 'catalog_product_entity', 'entity_id'),
            'related_id',
            $installer->getTable('catalog_product_entity'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Designnbuy PrintingMethod PrintingMethod To Product Linkage Table'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'designnbuy_printingmethod_quantityrange'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('designnbuy_printingmethod_quantityrange')
        )->addColumn(
            'quantityrange_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'QuantityRange ID'
        )->addColumn(
            'range_from',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false],
            'Quantity Range From'
        )->addColumn(
            'range_to',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false],
            'Quantity Range To'
        )->addColumn(
            'title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'QuantityRange Title'
        )->addColumn(
            'creation_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'QuantityRange Creation Time'
        )->addColumn(
            'update_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'QuantityRange Modification Time'
        )->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1'],
            'Is QuantityRange Active'
        )->addIndex(
            $setup->getIdxName(
                $installer->getTable('designnbuy_printingmethod_quantityrange'),
                ['title'],
                AdapterInterface::INDEX_TYPE_FULLTEXT
            ),
            ['title'],
            ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
        )->setComment(
            'Designnbuy PrintingMethod QuantityRange Table'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'designnbuy_printingmethod_colorcounter'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('designnbuy_printingmethod_colorcounter')
        )->addColumn(
            'colorcounter_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'ColorCounter ID'
        )->addColumn(
            'title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'ColorCounter Title'
        )->addColumn(
            'counter',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false],
            'Counter'
        )->addColumn(
            'creation_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'ColorCounter Creation Time'
        )->addColumn(
            'update_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'ColorCounter Modification Time'
        )->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1'],
            'Is ColorCounter Active'
        )->addIndex(
            $setup->getIdxName(
                $installer->getTable('designnbuy_printingmethod_colorcounter'),
                ['title'],
                AdapterInterface::INDEX_TYPE_FULLTEXT
            ),
            ['title'],
            ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
        )->setComment(
            'Designnbuy PrintingMethod ColorCounter Table'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'designnbuy_printingmethod_squarearea'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('designnbuy_printingmethod_squarearea')
        )->addColumn(
            'squarearea_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'SquareArea ID'
        )->addColumn(
            'title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'SquareArea Title'
        )->addColumn(
            'area',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false],
            'Counter'
        )->addColumn(
            'creation_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'SquareArea Creation Time'
        )->addColumn(
            'update_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'SquareArea Modification Time'
        )->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1'],
            'Is SquareArea Active'
        )->addIndex(
            $setup->getIdxName(
                $installer->getTable('designnbuy_printingmethod_squarearea'),
                ['title'],
                AdapterInterface::INDEX_TYPE_FULLTEXT
            ),
            ['title'],
            ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
        )->setComment(
            'Designnbuy PrintingMethod SquareArea Table'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'designnbuy_printingmethod_printablecolors'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('designnbuy_printingmethod_printablecolors')
        )->addColumn(
            'color_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Color ID'
        )->addColumn(
            'printingmethod_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [ 'nullable' => false, 'primary' => true],
            'Related Product ID'
        )->addColumn(
            'color_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Color Code'
        )->addColumn(
            'position',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false],
            'Position'
        )->addIndex(
            $installer->getIdxName('designnbuy_printingmethod_printablecolors', ['printingmethod_id']),
            ['printingmethod_id']
        )->addForeignKey(
            $installer->getFkName('designnbuy_printingmethod_printablecolors', 'printingmethod_id', 'designnbuy_printingmethod_printingmethod', 'printingmethod_id'),
            'printingmethod_id',
            $installer->getTable('designnbuy_printingmethod_printingmethod'),
            'printingmethod_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Designnbuy Printablecolors To Printingmethod Linkage Table'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'designnbuy_printingmethod_printingmethod_label'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('designnbuy_printingmethod_printablecolors_label')
        )->addColumn(
            'label_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Label Id'
        )->addColumn(
            'color_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'PrintingMethod Id'
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Store Id'
        )->addColumn(
            'label',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            [],
            'Label'
        )->addIndex(
            $installer->getIdxName(
                'designnbuy_printingmethod_printablecolors_label',
                ['color_id', 'store_id'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['color_id', 'store_id'],
            ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addIndex(
            $installer->getIdxName('designnbuy_printingmethod_printablecolors_label', ['store_id']),
            ['store_id']
        )->addForeignKey(
            $installer->getFkName('designnbuy_printingmethod_printablecolors_label', 'color_id', 'designnbuy_printingmethod_printablecolors', 'color_id'),
            'color_id',
            $installer->getTable('designnbuy_printingmethod_printablecolors'),
            'color_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('designnbuy_printingmethod_printablecolors_label', 'store_id', 'store', 'store_id'),
            'store_id',
            $installer->getTable('store'),
            'store_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Designnbuy PrintingMethod Printable Colors Label'
        );
        $installer->getConnection()->createTable($table);


        /**
         * Create table 'designnbuy_printingmethod_quantity_area_price'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('designnbuy_printingmethod_quantity_area_price')
        )->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'ID'
        )->addColumn(
            'printingmethod_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'primary' => true],
            'Related Printing Method ID'
        )->addColumn(
            'quantityrange_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'primary' => true],
            'Quantity Range ID'
        )->addColumn(
            'squarearea_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'primary' => true],
            'Square Area ID'
        )->addColumn(
            'position',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false],
            'Position'
        )->addIndex(
            $installer->getIdxName('designnbuy_printingmethod_quantity_area_price', ['printingmethod_id']),
            ['printingmethod_id']
        )->addForeignKey(
            $installer->getFkName('designnbuy_printingmethod_quantity_area_price', 'printingmethod_id', 'designnbuy_printingmethod_printingmethod', 'printingmethod_id'),
            'printingmethod_id',
            $installer->getTable('designnbuy_printingmethod_printingmethod'),
            'printingmethod_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('designnbuy_printingmethod_quantity_area_price', 'quantityrange_id', 'designnbuy_printingmethod_quantityrange', 'quantityrange_id'),
            'quantityrange_id',
            $installer->getTable('designnbuy_printingmethod_quantityrange'),
            'quantityrange_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('designnbuy_printingmethod_quantity_area_price', 'squarearea_id', 'designnbuy_printingmethod_squarearea', 'squarearea_id'),
            'squarearea_id',
            $installer->getTable('designnbuy_printingmethod_squarearea'),
            'squarearea_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Designnbuy Quantity Area Price To Printingmethod Linkage Table'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'designnbuy_printingmethod_quantity_area_price_side_price'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('designnbuy_printingmethod_quantity_area_price_side_price')
        )->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Id'
        )->addColumn(
            'quantity_area_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Quantity Area Id'
        )->addColumn(
            'side_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Side Id'
        )->addColumn(
            'price',
            \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
            null,
            [],
            'Price'
        )->addIndex(
            $installer->getIdxName('designnbuy_printingmethod_quantity_area_price', ['id']),
            ['quantity_area_id']
        )->addForeignKey(
            $installer->getFkName('designnbuy_printingmethod_quantity_area_price_side_price', 'quantity_area_id', 'designnbuy_printingmethod_quantity_area_price', 'id'),
            'quantity_area_id',
            $installer->getTable('designnbuy_printingmethod_quantity_area_price'),
            'id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Designnbuy Printingmethod Quantity Area Price Side Wise Price'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'designnbuy_printingmethod_quantity_color_price'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('designnbuy_printingmethod_quantity_color_price')
        )->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'ID'
        )->addColumn(
            'printingmethod_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'primary' => true],
            'Related Printing Method ID'
        )->addColumn(
            'quantityrange_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'primary' => true],
            'Quantity Range ID'
        )->addColumn(
            'colorcounter_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'primary' => true],
            ' ID'
        )->addColumn(
            'position',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false],
            'Position'
        )->addIndex(
            $installer->getIdxName('designnbuy_printingmethod_quantity_color_price', ['printingmethod_id']),
            ['printingmethod_id']
        )->addForeignKey(
            $installer->getFkName('designnbuy_printingmethod_quantity_color_price', 'printingmethod_id', 'designnbuy_printingmethod_printingmethod', 'printingmethod_id'),
            'printingmethod_id',
            $installer->getTable('designnbuy_printingmethod_printingmethod'),
            'printingmethod_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('designnbuy_printingmethod_quantity_color_price', 'quantityrange_id', 'designnbuy_printingmethod_quantityrange', 'quantityrange_id'),
            'quantityrange_id',
            $installer->getTable('designnbuy_printingmethod_quantityrange'),
            'quantityrange_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('designnbuy_printingmethod_quantity_color_price', 'colorcounter_id', 'designnbuy_printingmethod_colorcounter', 'colorcounter_id'),
            'colorcounter_id',
            $installer->getTable('designnbuy_printingmethod_colorcounter'),
            'colorcounter_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Designnbuy Quantity Color Price To Printingmethod Linkage Table'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'designnbuy_printingmethod_quantity_color_price_side_price'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('designnbuy_printingmethod_quantity_color_price_side_price')
        )->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Id'
        )->addColumn(
            'quantity_color_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Quantity Color Id'
        )->addColumn(
            'side_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Side Id'
        )->addColumn(
            'price',
            \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
            null,
            [],
            'Price'
        )->addIndex(
            $installer->getIdxName('designnbuy_printingmethod_quantity_color_price', ['id']),
            ['quantity_color_id']
        )->addForeignKey(
            $installer->getFkName('designnbuy_printingmethod_quantity_color_price_side_price', 'quantity_color_id', 'designnbuy_printingmethod_quantity_color_price', 'id'),
            'quantity_color_id',
            $installer->getTable('designnbuy_printingmethod_quantity_color_price'),
            'id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Designnbuy Printingmethod Quantity Color Side Wise Price'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'designnbuy_printingmethod_quantity_price'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('designnbuy_printingmethod_quantity_price')
        )->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'ID'
        )->addColumn(
            'printingmethod_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'primary' => true],
            'Related Printing Method ID'
        )->addColumn(
            'quantityrange_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false, 'primary' => true],
            'Quantity Range ID'
        )->addColumn(
            'position',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false],
            'Position'
        )->addIndex(
            $installer->getIdxName('designnbuy_printingmethod_quantity_price', ['printingmethod_id']),
            ['printingmethod_id']
        )->addForeignKey(
            $installer->getFkName('designnbuy_printingmethod_quantity_price', 'printingmethod_id', 'designnbuy_printingmethod_printingmethod', 'printingmethod_id'),
            'printingmethod_id',
            $installer->getTable('designnbuy_printingmethod_printingmethod'),
            'printingmethod_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('designnbuy_printingmethod_quantity_price', 'quantityrange_id', 'designnbuy_printingmethod_quantityrange', 'quantityrange_id'),
            'quantityrange_id',
            $installer->getTable('designnbuy_printingmethod_quantityrange'),
            'quantityrange_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Designnbuy Quantity Price To Printingmethod Linkage Table'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'designnbuy_printingmethod_quantity_price_side_price'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('designnbuy_printingmethod_quantity_price_side_price')
        )->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Id'
        )->addColumn(
            'quantity_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Quantity Id'
        )->addColumn(
            'side_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Side Id'
        )->addColumn(
            'price',
            \Magento\Framework\DB\Ddl\Table::TYPE_FLOAT,
            null,
            [],
            'Price'
        )->addIndex(
            $installer->getIdxName('designnbuy_printingmethod_quantity_price', ['id']),
            ['quantity_id']
        )->addForeignKey(
            $installer->getFkName('designnbuy_printingmethod_quantity_price_side_price', 'quantity_id', 'designnbuy_printingmethod_quantity_price', 'id'),
            'quantity_id',
            $installer->getTable('designnbuy_printingmethod_quantity_price'),
            'id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Designnbuy Printingmethod Quantity Side Wise Price'
        );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();

    }
}
