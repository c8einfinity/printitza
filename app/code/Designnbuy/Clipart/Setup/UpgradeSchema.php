<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Clipart\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Clipart schema update
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

            foreach (['designnbuy_clipart_clipart_relatedclipart', 'designnbuy_clipart_clipart_relatedproduct'] as $tableName) {
                // Get module table
                $tableName = $setup->getTable($tableName);

                // Check if the table already exists
                if ($connection->isTableExists($tableName) == true) {

                    $columns = [
                        'position' => [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                            'nullable' => false,
                            'comment' => 'Position',
                        ],
                    ];

                    foreach ($columns as $name => $definition) {
                        $connection->addColumn($tableName, $name, $definition);
                    }

                }
            }

            if (version_compare($version, '2.3.0') < 0) {

                /**
                 * Create table 'designnbuy_clipart_tag'
                 */
                $table = $setup->getConnection()->newTable(
                    $setup->getTable('designnbuy_clipart_tag')
                )->addColumn(
                    'tag_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'nullable' => false, 'primary' => true],
                    'Tag ID'
                )->addColumn(
                    'title',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    ['nullable' => true],
                    'Tag Title'
                )->addColumn(
                    'identifier',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    100,
                    ['nullable' => true, 'default' => null],
                    'Tag String Identifier'
                )->addIndex(
                    $setup->getIdxName('designnbuy_clipart_tag', ['identifier']),
                    ['identifier']
                )->setComment(
                    'Designnbuy Clipart Tag Table'
                );
                $setup->getConnection()->createTable($table);

                /**
                 * Create table 'designnbuy_clipart_clipart_tag'
                 */
                $table = $setup->getConnection()->newTable(
                    $setup->getTable('designnbuy_clipart_clipart_tag')
                )->addColumn(
                    'clipart_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'primary' => true],
                    'Clipart ID'
                )->addColumn(
                    'tag_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false, 'primary' => true],
                    'Tag ID'
                )->addIndex(
                    $setup->getIdxName('designnbuy_clipart_clipart_tag', ['tag_id']),
                    ['tag_id']
                )->addForeignKey(
                    $setup->getFkName('designnbuy_clipart_clipart_tag', 'clipart_id', 'designnbuy_clipart_clipart', 'clipart_id'),
                    'clipart_id',
                    $setup->getTable('designnbuy_clipart_clipart'),
                    'clipart_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )->addForeignKey(
                    $setup->getFkName('designnbuy_clipart_clipart_tag', 'tag_id', 'designnbuy_clipart_tag', 'tag_id'),
                    'tag_id',
                    $setup->getTable('designnbuy_clipart_tag'),
                    'tag_id',
                    \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
                )->setComment(
                    'Designnbuy Clipart Clipart To Category Linkage Table'
                );
                $setup->getConnection()->createTable($table);
            }

            if (version_compare($version, '2.6.0') < 0) {
                $connection->addColumn(
                    $setup->getTable('designnbuy_clipart_clipart'),
                    'image',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length' => 255,
                        'nullable' => true,
                        'comment' => 'Thumbnail Image',
                    ]
                );
            }



        }

        if (version_compare($version, '2.6.0') < 0) {
            /**
             * Create table 'designnbuy_clipart_category_relatedproduct'
             */
            $table = $installer->getConnection()->newTable(
                $installer->getTable('designnbuy_clipart_category_relatedproduct')
            )->addColumn(
                'category_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'primary' => true],
                'Category ID'
            )->addColumn(
                'related_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Related Product ID'
            )->addColumn(
                'position',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false, 'default' => '0'],
                'Position'
            )->addIndex(
                $installer->getIdxName('designnbuy_clipart_category_relatedproduct', ['related_id']),
                ['related_id']
            )->addForeignKey(
                $installer->getFkName('designnbuy_clipart_category_relatedproduct', 'category_id', 'designnbuy_clipart_category', 'category_id'),
                'category_id',
                $installer->getTable('designnbuy_clipart_category'),
                'category_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName('designnbuy_clipart_category_relatedproduct', 'related_id', 'catalog_product_entity', 'entity_id'),
                'related_id',
                $installer->getTable('catalog_product_entity'),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment(
                'Designnbuy Clipart Category To Product Linkage Table'
            );
            $installer->getConnection()->createTable($table);
        }


        $setup->endSetup();
    }
}
