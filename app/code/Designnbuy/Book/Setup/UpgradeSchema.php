<?php
/**
 * Copyright © 2017 Designnbuy. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Designnbuy\Book\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{

    const UNIQUE_FIELD_SKU = 'unique_field_sku';
    const CATALOG_PRODUCT_OPTION_TABLE = 'catalog_product_option';
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();


        if (version_compare($context->getVersion(), '1.0.0', '<')) {
            //add unique_field_sku for catalog_product_option table
            $installer->getConnection()->addColumn(
                $setup->getTable(static::CATALOG_PRODUCT_OPTION_TABLE),
                static::UNIQUE_FIELD_SKU,
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 50,
                    'nullable' => true,
                    'comment' => 'Unique Field (added by Designnbuy)',
                    //'after' => 'one_time'
                ]
            );
        }

        $installer->endSetup();
    }
}
