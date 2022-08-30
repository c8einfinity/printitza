<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Merchandise\Setup;

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
         * Create table 'designnbuy_merchandise_product_sides_configuration'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('designnbuy_merchandise_product_sides_configuration')
        )->addColumn(
            'value_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Value ID'
        )->addColumn(
            'label',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Title'
        )->addColumn(
            'side_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'primary' => false],
            'Side ID'
        )->addColumn(
            'product_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Related Product ID'
        )->addColumn(
            'image',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Side Image'
        )->addColumn(
            'color_image',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Color Image'
        )->addColumn(
            'mask_image',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Mask Image'
        )->addColumn(
            'overlay_image',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Overlay Image'
        )->addColumn(
            'configure_areas',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Configure Areas'
        )->addColumn(
            'position',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => false],
            'Position'
        )->addIndex(
            $installer->getIdxName('designnbuy_merchandise_product_sides_configuration', ['product_id']),
            ['product_id']
        )->addForeignKey(
            $installer->getFkName('designnbuy_merchandise_product_sides_configuration', 'product_id', 'catalog_product_entity', 'entity_id'),
            'product_id',
            $installer->getTable('catalog_product_entity'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Designnbuy Sides Configuration To Product Linkage Table'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'designnbuy_merchandise_product_sides_configuration_label'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('designnbuy_merchandise_product_sides_configuration_label')
        )->addColumn(
            'label_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Label Id'
        )->addColumn(
            'value_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Side Id'
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
                'designnbuy_merchandise_product_sides_configuration_label',
                ['value_id', 'store_id'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['value_id', 'store_id'],
            ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
        )->addIndex(
            $installer->getIdxName('designnbuy_merchandise_product_sides_configuration_label', ['store_id']),
            ['store_id']
        )->addForeignKey(
            $installer->getFkName('designnbuy_merchandise_product_sides_configuration_label', 'value_id', 'designnbuy_merchandise_product_sides_configuration', 'value_id'),
            'value_id',
            $installer->getTable('designnbuy_merchandise_product_sides_configuration'),
            'value_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('designnbuy_merchandise_product_sides_configuration_label', 'store_id', 'store', 'store_id'),
            'store_id',
            $installer->getTable('store'),
            'store_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Sides Label'
        );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'designnbuy_merchandise_product_sides_configuration_area'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('designnbuy_merchandise_product_sides_configuration_area')
        )->addColumn(
            'area_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Area ID'
        )->addColumn(
            'side_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Side ID'
        )->addColumn(
            'configarea_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => false, 'nullable' => false],
            'Configarea Id'
        )->addColumn(
            'product_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Related Product ID'
        )->addColumn(
            'area',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true],
            'Side Image'
        )->addColumn(
            'position',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['nullable' => true],
            'Position'
        )->addIndex(
            $installer->getIdxName('designnbuy_merchandise_product_sides_configuration_area', ['product_id']),
            ['product_id']
        )->addForeignKey(
            $installer->getFkName('designnbuy_merchandise_product_sides_configuration_area', 'side_id', 'designnbuy_merchandise_product_sides_configuration', 'value_id'),
            'side_id',
            $installer->getTable('designnbuy_merchandise_product_sides_configuration'),
            'value_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('designnbuy_merchandise_product_sides_configuration_area', 'configarea_id', 'designnbuy_configarea_configarea', 'configarea_id'),
            'configarea_id',
            $installer->getTable('designnbuy_configarea_configarea'),
            'configarea_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('designnbuy_merchandise_product_sides_configuration_area', 'product_id', 'catalog_product_entity', 'entity_id'),
            'product_id',
            $installer->getTable('catalog_product_entity'),
            'entity_id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment(
            'Designnbuy Sides Configuration Configure Area'
        );
        $installer->getConnection()->createTable($table);
    }
}
