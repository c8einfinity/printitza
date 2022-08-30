<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Designidea\Setup;

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

        if (version_compare($version, '1.0.1') < 0) 
        {
            /**
             * Create table 'designnbuy_designidea_upsell_designidea'
             */
            $table = $setup->getConnection()->newTable(
                $setup->getTable('designnbuy_designidea_upsell_designidea')
            )->addColumn(
                'designidea_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'unsigned' => true, 'primary' => true],
                'Designidea ID'
            )->addColumn(
                'upsell_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Upsell Design Id'
            )
            ->addColumn(
                'position',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'default' => '0'],
                'Position'
            )->addIndex(
                $installer->getIdxName('designnbuy_designidea_upsell_designidea', ['upsell_id']),
                ['upsell_id']
            )->addForeignKey(
                $installer->getFkName('designnbuy_designidea_upsell_designidea', 'upsell_id', 'designnbuy_designidea_entity', 'entity_id'),
                'upsell_id',
                $installer->getTable('designnbuy_designidea_entity'),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment(
                'Designnbuy Designidea To Upsell Linkage Table'
            );
            $setup->getConnection()->createTable($table);

            /**
             * Create table 'designnbuy_designidea_cross_designidea'
             */
            $table = $setup->getConnection()->newTable(
                $setup->getTable('designnbuy_designidea_cross_designidea')
            )->addColumn(
                'designidea_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'unsigned' => true, 'primary' => true],
                'Design Idea ID'
            )->addColumn(
                'crosssell_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Cross Sell Design Id'
            )
            ->addColumn(
                'position',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'default' => '0'],
                'Position'
            )->addIndex(
                $installer->getIdxName('designnbuy_designidea_cross_designidea', ['crosssell_id']),
                ['crosssell_id']
            )->addForeignKey(
                $installer->getFkName('designnbuy_designidea_cross_designidea', 'crosssell_id', 'designnbuy_designidea_entity', 'entity_id'),
                'crosssell_id',
                $installer->getTable('designnbuy_designidea_entity'),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment(
                'Designnbuy Designidea To Cross Sell Linkage Table'
            );
            $setup->getConnection()->createTable($table);        
        }
        $setup->endSetup();
    }
}
