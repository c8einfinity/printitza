<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Font\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Font schema update
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

            foreach (['designnbuy_font_font', 'designnbuy_font_font_relatedfont', 'designnbuy_font_font_relatedproduct'] as $tableName) {
                // Get module table
                $tableName = $setup->getTable($tableName);

                // Check if the table already exists
                if ($connection->isTableExists($tableName) == true) {

                    $columns = [
                        'position' => [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                            'nullable' => true,
                            'comment' => 'Position',
                        ],
                    ];

                    foreach ($columns as $name => $definition) {
                        $connection->addColumn($tableName, $name, $definition);
                    }

                }
            }

            $connection->addColumn(
                $setup->getTable('designnbuy_font_font'),
                'woff',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Font Woff',
                ]
            );

            $connection->addColumn(
                $setup->getTable('designnbuy_font_font'),
                'js',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Font Js',
                ]
            );

            $connection->addColumn(
                $setup->getTable('designnbuy_font_font'),
                'css',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Font CSS',
                ]
            );
        }
        if (version_compare($version, '2.5.1') < 0) {
            $connection->addColumn(
                $setup->getTable('designnbuy_font_font'),
                'ttf',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Font Ttf',
                ]
            );
            $connection->addColumn(
                $setup->getTable('designnbuy_font_font'),
                'ttfbold',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Font Ttf Bold',
                ]
            );
            $connection->addColumn(
                $setup->getTable('designnbuy_font_font'),
                'ttfitalic',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Font Ttf Italic',
                ]
            );
            $connection->addColumn(
                $setup->getTable('designnbuy_font_font'),
                'ttfbolditalic',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Font Ttf Bold Italic',
                ]
            );
            $connection->addColumn(
                $setup->getTable('designnbuy_font_font'),
                'ttf_tcpdf',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Font Ttf',
                ]
            );
            $connection->addColumn(
                $setup->getTable('designnbuy_font_font'),
                'ttfbold_tcpdf',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Font Ttf Bold',
                ]
            );
            $connection->addColumn(
                $setup->getTable('designnbuy_font_font'),
                'ttfitalic_tcpdf',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Font Ttf Italic',
                ]
            );
            $connection->addColumn(
                $setup->getTable('designnbuy_font_font'),
                'ttfbolditalic_tcpdf',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Font Ttf Bold Italic',
                ]
            );
        }
        $setup->endSetup();
    }
}
