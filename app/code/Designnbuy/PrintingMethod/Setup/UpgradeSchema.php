<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\PrintingMethod\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * PrintingMethod schema update
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $setup->startSetup();

        $version = $context->getVersion();
        
        $connection = $setup->getConnection();

        if (version_compare($version, '2.0.1') < 0) {

            /*$connection->addColumn(
                $setup->getTable('designnbuy_printingmethod_printingmethod'),
                'name_price',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    '10,2',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' =>'Name Price'
                ]
            );

            $connection->addColumn(
                $setup->getTable('designnbuy_printingmethod_printingmethod'),
                'number_price',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                    '10,2',
                    'default' => 0.00,
                    'nullable' => true,
                    'comment' =>'Number Price'
                ]
            );*/

            $tableName = $setup->getTable('designnbuy_printingmethod_printingmethod');
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $columns = [
                    'name_price' => [
                        'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        'length'    => '12,4',
                        'nullable'  => true,
                        'default'   => 0.00,
                        'comment'   => 'Name Price'
                    ],
                    'number_price' => [
                        'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
                        'length'    => '12,4',
                        'nullable'  => true,
                        'default'   => 0.00,
                        'comment'   => 'Number Price'
                    ]
                ];
                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
            }

        }

        if (version_compare($version, '2.0.1') < 0) {
            /**
             * Create table 'designnbuy_printingmethod_printingmethod_color_category'
             */
            $table = $installer->getConnection()->newTable(
                $installer->getTable('designnbuy_printingmethod_printingmethod_color_category')
            )->addColumn(
                'printingmethod_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'primary' => true],
                'Color ID'
            )->addColumn(
                'category_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'primary' => true],
                'Category ID'
            )->addIndex(
                $installer->getIdxName('designnbuy_printingmethod_printingmethod_color_category', ['category_id']),
                ['category_id']
            )->addForeignKey(
                $installer->getFkName('designnbuy_printingmethod_printingmethod_color_category', 'printingmethod_id', 'designnbuy_printingmethod_printingmethod', 'printingmethod_id'),
                'printingmethod_id',
                $installer->getTable('designnbuy_printingmethod_printingmethod'),
                'printingmethod_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName('designnbuy_printingmethod_printingmethod_color_category', 'category_id', 'designnbuy_color_category', 'category_id'),
                'category_id',
                $installer->getTable('designnbuy_color_category'),
                'category_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment(
                'Designnbuy Printingmethod To Color Category Linkage Table'
            );
            $installer->getConnection()->createTable($table);
        }

        if (version_compare($version, '2.0.1') < 0) {
            $tableName = $setup->getTable('designnbuy_printingmethod_colorcounter');
            if ($setup->getConnection()->isTableExists($tableName) == true) {
                $columns = [
                    'range_from' => [
                        'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'length'    => '11',
                        'nullable'  => false,
                        'comment'   => 'Color Range From'
                    ],
                    'range_to' => [
                        'type'      => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'length'    => '11',
                        'nullable'  => false,
                        'comment'   => 'Color Range To'
                    ]
                ];
                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
            }
        }

        $setup->endSetup();
    }
}
