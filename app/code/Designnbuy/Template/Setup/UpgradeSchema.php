<?php

namespace Designnbuy\Template\Setup;

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
             * Create table 'designnbuy_template_upsell_template'
             */
            $table = $setup->getConnection()->newTable(
                $setup->getTable('designnbuy_template_upsell_template')
            )->addColumn(
                'template_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'unsigned' => true, 'primary' => true],
                'Template Id'
            )->addColumn(
                'upsell_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Upsell Template Id'
            )->addColumn(
                'position',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'default' => '0'],
                'Position'
            )->addIndex(
                $installer->getIdxName('designnbuy_template_upsell_template', ['upsell_id']),
                ['upsell_id']
            )->addForeignKey(
                $installer->getFkName('designnbuy_template_upsell_template', 'upsell_id', 'designnbuy_template_entity', 'entity_id'),
                'upsell_id',
                $installer->getTable('designnbuy_template_entity'),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment(
                'Designnbuy Template To Upsell Linkage Table'
            );
            $setup->getConnection()->createTable($table);

            /**
             * Create table 'designnbuy_template_crosssell_template'
             */
            
            $table = $setup->getConnection()->newTable(
                $setup->getTable('designnbuy_template_crosssell_template')
            )->addColumn(
                'template_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'unsigned' => true, 'primary' => true],
                'Template ID'
            )->addColumn(
                'crosssell_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Cross Sell Design Id'
            )->addColumn(
                'position',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'default' => '0'],
                'Position'
            )->addIndex(
                $installer->getIdxName('designnbuy_template_crosssell_template', ['crosssell_id']),
                ['crosssell_id']
            )->addForeignKey(
                $installer->getFkName('designnbuy_template_crosssell_template', 'crosssell_id', 'designnbuy_template_entity', 'entity_id'),
                'crosssell_id',
                $installer->getTable('designnbuy_template_entity'),
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )->setComment(
                'Designnbuy Template To Cross Sell Linkage Table'
            );
            $setup->getConnection()->createTable($table);        
        }
        $setup->endSetup();
    }
}