<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Background\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;

/**
 * Background setup
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
         * Create table 'designnbuy_background_background'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('designnbuy_background_background')
        )->addColumn(
            'background_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Background ID'
        )->addColumn(
            'title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Background Title'
        )->addColumn(
            'unit',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            100,
            ['nullable' => true, 'default' => null],
            'Background Image Unit'
		)->addColumn(
            'creation_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Background Creation Time'
        )->addColumn(
            'update_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Background Modification Time'
        )->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1'],
            'Is Background Active'
        )->addIndex(
            $setup->getIdxName(
                $installer->getTable('designnbuy_background_background'),
                ['title'],
                AdapterInterface::INDEX_TYPE_FULLTEXT
            ),
            ['title'],
            ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
        )->setComment(
            'Designnbuy Background Background Table'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'designnbuy_background_background_store'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('designnbuy_background_background_store')
        )->addColumn(
            'background_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'primary' => true],
            'Background ID'
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Store ID'
        )->addIndex(
            $installer->getIdxName('designnbuy_background_background_store', ['store_id']),
            ['store_id']
        )->addForeignKey(
            $installer->getFkName('designnbuy_background_background_store', 'background_id', 'designnbuy_background_background', 'background_id'),
            'background_id',
            $installer->getTable('designnbuy_background_background'),
            'background_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('designnbuy_background_background_store', 'store_id', 'store', 'store_id'),
            'store_id',
            $installer->getTable('store'),
            'store_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Designnbuy Background Background To Store Linkage Table'
        );
        $installer->getConnection()->createTable($table);


        $table = $installer->getConnection()->newTable(
            $installer->getTable('designnbuy_background_category')
        )->addColumn(
            'category_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'Category ID'
        )->addColumn(
            'title',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Category Title'
        )->addColumn(
            'path',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Category Path'
        )->addColumn(
            'position',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false],
            'Category Position'
        )->addColumn(
            'creation_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Background Category Creation Time'
        )->addColumn(
            'update_time',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Background Category Modification Time'
        )->addColumn(
            'is_active',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '1'],
            'Is Category Active'
        )->addIndex(
            $setup->getIdxName(
                $installer->getTable('designnbuy_background_category'),
                ['title'],
                AdapterInterface::INDEX_TYPE_FULLTEXT
            ),
            ['title'],
            ['type' => AdapterInterface::INDEX_TYPE_FULLTEXT]
        )->setComment(
            'Designnbuy Background Category Table'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'designnbuy_background_category_store'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('designnbuy_background_category_store')
        )->addColumn(
            'category_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'primary' => true],
            'Category ID'
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Store ID'
        )->addIndex(
            $installer->getIdxName('designnbuy_background_category_store', ['store_id']),
            ['store_id']
        )->addForeignKey(
            $installer->getFkName('designnbuy_background_category_store', 'category_id', 'designnbuy_background_category', 'category_id'),
            'category_id',
            $installer->getTable('designnbuy_background_category'),
            'category_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('designnbuy_background_category_store', 'store_id', 'store', 'store_id'),
            'store_id',
            $installer->getTable('store'),
            'store_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Designnbuy Background Category To Store Linkage Table'
        );
        $installer->getConnection()->createTable($table);


        /**
         * Create table 'designnbuy_background_background_category'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('designnbuy_background_background_category')
        )->addColumn(
            'background_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'primary' => true],
            'Background ID'
        )->addColumn(
            'category_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'primary' => true],
            'Category ID'
        )->addIndex(
            $installer->getIdxName('designnbuy_background_background_category', ['category_id']),
            ['category_id']
        )->addForeignKey(
            $installer->getFkName('designnbuy_background_background_category', 'background_id', 'designnbuy_background_background', 'background_id'),
            'background_id',
            $installer->getTable('designnbuy_background_background'),
            'background_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('designnbuy_background_background_category', 'category_id', 'designnbuy_background_category', 'category_id'),
            'category_id',
            $installer->getTable('designnbuy_background_category'),
            'category_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Designnbuy Background Background To Category Linkage Table'
        );
        $installer->getConnection()->createTable($table);


        /**
         * Create table 'designnbuy_background_background_relatedproduct'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('designnbuy_background_background_relatedproduct')
        )->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'nullable' => false, 'primary' => true],
            'ID'
        )->addColumn(
            'background_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'primary' => false],
            'Background ID'
        )->addColumn(
            'related_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => false],
            'Related Product ID'
        )->addIndex(
            $installer->getIdxName('designnbuy_background_background_relatedproduct', ['related_id']),
            ['related_id']
        )->addForeignKey(
            $installer->getFkName('designnbuy_background_background_relatedproduct', 'background_id', 'designnbuy_background_background', 'background_id'),
            'background_id',
            $installer->getTable('designnbuy_background_background'),
            'background_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('designnbuy_background_background_relatedproduct', 'related_id', 'catalog_product_entity', 'entity_id'),
            'related_id',
            $installer->getTable('catalog_product_entity'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Designnbuy Background Background To Product Linkage Table'
        );
        $installer->getConnection()->createTable($table);


        /**
         * Create table 'designnbuy_background_background_relatedproduct'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('designnbuy_background_background_relatedbackground')
        )->addColumn(
            'background_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'primary' => true],
            'Background ID'
        )->addColumn(
            'related_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'primary' => true],
            'Related Background ID'
        )->addIndex(
            $installer->getIdxName('designnbuy_background_background_relatedproduct', ['related_id']),
            ['related_id']
        )->addForeignKey(
            $installer->getFkName('designnbuy_background_background_relatedproduct1', 'background_id', 'designnbuy_background_background', 'background_id'),
            'background_id',
            $installer->getTable('designnbuy_background_background'),
            'background_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('designnbuy_background_background_relatedproduct2', 'related_id', 'designnbuy_background_background', 'background_id'),
            'background_id',
            $installer->getTable('designnbuy_background_background'),
            'background_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Designnbuy Background Background To Background Linkage Table'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'designnbuy_background_background_label'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('designnbuy_background_background_images')
        )->addColumn(
            'image_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Label Id'
        )->addColumn(
            'background_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false],
            'Background Id'
        )->addColumn(
            'output',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Output Background Image'
        )->addColumn(
            'display',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Display Background Image'
        )->addColumn(
            'width',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Image Width'
        )->addColumn(
            'height',
            \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Image Height'
        )->addForeignKey(
            $installer->getFkName('designnbuy_background_background_images', 'background_id', 'designnbuy_background_background', 'background_id'),
            'background_id',
            $installer->getTable('designnbuy_background_background'),
            'background_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Designnbuy Background Background Images To Background Linkage Table'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'designnbuy_background_background_label'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('designnbuy_background_background_label')
        )->addColumn(
            'label_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Label Id'
        )->addColumn(
            'background_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false],
            'Background Id'
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
                'designnbuy_background_background_label',
                ['background_id', 'store_id'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['background_id', 'store_id'],
            ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addIndex(
            $installer->getIdxName('designnbuy_background_background_label', ['store_id']),
            ['store_id']
        )->addForeignKey(
            $installer->getFkName('designnbuy_background_background_label', 'background_id', 'designnbuy_background_background', 'background_id'),
            'background_id',
            $installer->getTable('designnbuy_background_background'),
            'background_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('designnbuy_background_background_label', 'store_id', 'store', 'store_id'),
            'store_id',
            $installer->getTable('store'),
            'store_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Background Background Label'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'designnbuy_background_category_label'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('designnbuy_background_category_label')
        )->addColumn(
            'label_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Label Id'
        )->addColumn(
            'category_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false],
            'Category Id'
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
                'designnbuy_background_category_label',
                ['category_id', 'store_id'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['category_id', 'store_id'],
            ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addIndex(
            $installer->getIdxName('designnbuy_background_category_label', ['store_id']),
            ['store_id']
        )->addForeignKey(
            $installer->getFkName('designnbuy_background_category_label', 'category_id', 'designnbuy_background_category', 'category_id'),
            'category_id',
            $installer->getTable('designnbuy_background_category'),
            'category_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('designnbuy_background_category_label', 'store_id', 'store', 'store_id'),
            'store_id',
            $installer->getTable('store'),
            'store_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Background Category Label'
        );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
